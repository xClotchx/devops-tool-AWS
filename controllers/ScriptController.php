<?php
require_once "models/script.php";
require_once "config/database.php"; 

// Cargamos el SDK de AWS desde el autoloader de Composer
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class ScriptController {
    private $db;
    private $scriptModel;
    private $s3;
    private $bucket;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->scriptModel = new Script($this->db);

        // Inicializamos el cliente de AWS S3 con las variables de entorno configuradas en Docker
        $this->bucket = $_ENV['AWS_BUCKET_NAME'] ?? getenv('AWS_BUCKET_NAME');
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['AWS_REGION'] ?? getenv('AWS_REGION') ?: 'us-east-1',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? getenv('AWS_ACCESS_KEY_ID'),
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    // LISTAR CON FILTRO Y ENLACES PRIVADOS TEMPORALES
    public function index() {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        
        $category_filter = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : null;
        $stmt = $this->scriptModel->readAll($category_filter);
        $scripts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Modificamos el arreglo de scripts para inyectar la URL Firmada Temporal de S3
        foreach ($scripts as &$script) {
            if (!empty($script['image_path']) && strpos($script['image_path'], 'uploads/') === 0) {
                try {
                    // Generamos un comando GetObject para la clave interna guardada
                    $cmd = $this->s3->getCommand('GetObject', [
                        'Bucket' => $this->bucket,
                        'Key'    => $script['image_path']
                    ]);
                    // La URL expira automáticamente en 10 minutos
                    $request = $this->s3->createPresignedRequest($cmd, '+10 minutes');
                    $script['image_url_firmada'] = (string)$request->getUri();
                } catch (Exception $e) {
                    error_log("Error generando URL firmada: " . $e->getMessage());
                    $script['image_url_firmada'] = null;
                }
            } else {
                $script['image_url_firmada'] = null;
            }
        }
        unset($script); // Rompemos la referencia del puntero foreach
        
        require_once "views/dashboard.php";
    }

    // PROCESAR Y GUARDAR EN S3 DE FORMA TOTALMENTE PRIVADA (CON CAZADOR DE ERRORES)
    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->scriptModel->user_id = $_SESSION['user_id'];
            
            $this->scriptModel->title = $_POST['title'];
            $this->scriptModel->description = $_POST['description'];
            $this->scriptModel->code_content = $_POST['code_content'];
            $this->scriptModel->instructions = $_POST['instructions'];
            $this->scriptModel->category_id = $_POST['category_id'];
            
            $s3_key = null;
            if (isset($_FILES['script_image']) && $_FILES['script_image']['error'] == 0) {
                $file_tmp = $_FILES['script_image']['tmp_name'];
                $file_extension = pathinfo($_FILES["script_image"]["name"], PATHINFO_EXTENSION);
                $image_name = time() . "_" . uniqid() . "." . $file_extension;
                $s3_key = 'uploads/' . $image_name;

                try {
                    // Subida directa al Bucket de forma privada
                    $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $s3_key,
                        'SourceFile' => $file_tmp
                    ]);
                } catch (S3Exception $e) {
                    // DETENER LA EJECUCIÓN PARA VER EL ERROR EN EL NAVEGADOR
                    echo "<h2>🚨 Error del SDK de AWS S3 al intentar subir el archivo:</h2>";
                    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                    echo "<h3>Variables actuales leídas por PHP:</h3>";
                    echo "Bucket: " . htmlspecialchars($this->bucket) . "<br>";
                    echo "Región: " . htmlspecialchars($this->s3->getRegion()) . "<br>";
                    die();
                } catch (Exception $e) {
                    echo "<h2>🚨 Error General del Sistema:</h2>";
                    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                    die();
                }
            }

            // Guardamos únicamente la KEY relativa (ej: uploads/archivo.png) en la DB
            $this->scriptModel->image_path = $s3_key;

            if ($this->scriptModel->create()) {
                header("Location: index.php?action=index&success=created");
            } else {
                header("Location: index.php?action=index&error=failed");
            }
            exit();
        }
    }

    // MOSTRAR FORMULARIO DE EDICIÓN
    public function edit($id) {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        $script = $this->scriptModel->readOne($id);
        if ($script) {
            // Generamos también la URL firmada para que se vea la foto actual en la vista de edición
            if (!empty($script['image_path']) && strpos($script['image_path'], 'uploads/') === 0) {
                $cmd = $this->s3->getCommand('GetObject', [
                    'Bucket' => $this->bucket,
                    'Key'    => $script['image_path']
                ]);
                $request = $this->s3->createPresignedRequest($cmd, '+10 minutes');
                $script['image_url_firmada'] = (string)$request->getUri();
            } else {
                $script['image_url_firmada'] = null;
            }

            require_once "views/edit.php";
        } else {
            header("Location: index.php?action=index");
        }
    }

    // PROCESAR ACTUALIZACIÓN Y SOBREESCRITURA DE ASSETS
    public function updateScript($id) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->scriptModel->user_id = $_SESSION['user_id'];
            $currentScript = $this->scriptModel->readOne($id);
            
            $this->scriptModel->id = $id;
            $this->scriptModel->title = $_POST['title'];
            $this->scriptModel->description = $_POST['description'];
            $this->scriptModel->code_content = $_POST['code_content'];
            $this->scriptModel->instructions = $_POST['instructions'];
            $this->scriptModel->category_id = $_POST['category_id'];

            // CASO A: El usuario decidió borrar la imagen actual
            if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
                $this->scriptModel->image_path = null;
                
                if (!empty($currentScript['image_path'])) {
                    $this->deleteFromS3($currentScript['image_path']);
                }
            } 
            // CASO B: El usuario subió una nueva imagen para reemplazar la vieja
            elseif (isset($_FILES['script_image']) && $_FILES['script_image']['error'] == 0) {
                $file_tmp = $_FILES['script_image']['tmp_name'];
                $file_extension = pathinfo($_FILES["script_image"]["name"], PATHINFO_EXTENSION);
                $image_name = time() . "_" . uniqid() . "." . $file_extension;
                $s3_key = 'uploads/' . $image_name;

                try {
                    $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $s3_key,
                        'SourceFile' => $file_tmp
                    ]);
                    
                    $this->scriptModel->image_path = $s3_key;
                    
                    // Si ya tenía una imagen previa, la borramos de S3 para mantener limpio el bucket
                    if (!empty($currentScript['image_path'])) {
                        $this->deleteFromS3($currentScript['image_path']);
                    }
                } catch (S3Exception $e) {
                    error_log("Error al actualizar en S3: " . $e->getMessage());
                    $this->scriptModel->image_path = $currentScript['image_path']; // Mantiene la anterior si falla
                }
            } 
            // CASO C: No hubo cambios en la imagen
            else {
                $this->scriptModel->image_path = $currentScript['image_path'];
            }

            if ($this->scriptModel->update()) {
                header("Location: index.php?action=index&success=updated");
            } else {
                header("Location: index.php?action=index&error=update_failed");
            }
            exit();
        }
    }

    // BORRAR SCRIPT Y LIMPIAR S3
    public function deleteScript($id) {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        $currentScript = $this->scriptModel->readOne($id);
        
        if ($this->scriptModel->delete($id)) {
            if (!empty($currentScript['image_path'])) {
                $this->deleteFromS3($currentScript['image_path']);
            }
            header("Location: index.php?action=index&success=deleted");
        } else {
            header("Location: index.php?action=index&error=delete_failed");
        }
        exit();
    }

    // FUNCIÓN AUXILIAR PRIVADA CORREGIDA PARA MANEJAR LA RUTA DIRECTA (KEY)
    private function deleteFromS3($s3_key) {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $s3_key
            ]);
        } catch (S3Exception $e) {
            error_log("Error al eliminar objeto de S3: " . $e->getMessage());
        }
    }
}
?>