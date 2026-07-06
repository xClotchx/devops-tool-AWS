<?php
require_once __DIR__ . '/../models/Script.php'; //  Esto funciona perfecto en local y producción
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
            'region'  => $_ENV['AWS_REGION'] ?? getenv('AWS_REGION'),
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? getenv('AWS_ACCESS_KEY_ID'),
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? getenv('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }

    // LISTAR CON FILTRO
    public function index() {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        
        $category_filter = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : null;
        $stmt = $this->scriptModel->readAll($category_filter);
        $scripts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once "views/dashboard.php";
    }

    // PROCESAR Y GUARDAR DIRECTAMENTE EN AWS S3
    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->scriptModel->user_id = $_SESSION['user_id'];
            
            $this->scriptModel->title = $_POST['title'];
            $this->scriptModel->description = $_POST['description'];
            $this->scriptModel->code_content = $_POST['code_content'];
            $this->scriptModel->instructions = $_POST['instructions'];
            $this->scriptModel->category_id = $_POST['category_id'];
            
            $image_url = null;
            if (isset($_FILES['script_image']) && $_FILES['script_image']['error'] == 0) {
                $file_tmp = $_FILES['script_image']['tmp_name'];
                $file_extension = pathinfo($_FILES["script_image"]["name"], PATHINFO_EXTENSION);
                $image_name = time() . "_" . uniqid() . "." . $file_extension;

                try {
                    // Subida directa al Bucket en AWS S3
                    $result = $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => 'uploads/' . $image_name,
                        'SourceFile' => $file_tmp,
                        'ACL'    => 'public-read' // Para que las imágenes sean visibles en la web
                    ]);
                    
                    // Guardamos la URL pública completa en AWS RDS
                    $image_url = $result['ObjectURL'];
                } catch (S3Exception $e) {
                    error_log("Error al subir a S3 en store: " . $e->getMessage());
                }
            }

            $this->scriptModel->image_path = $image_url;

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
            require_once "views/edit.php";
        } else {
            header("Location: index.php?action=index");
        }
    }

    // PROCESAR ACTUALIZACIÓN CON MANEJO DE AWS S3
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

                try {
                    $result = $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => 'uploads/' . $image_name,
                        'SourceFile' => $file_tmp,
                        'ACL'    => 'public-read'
                    ]);
                    
                    $this->scriptModel->image_path = $result['ObjectURL'];
                    
                    // Si ya tenía una imagen previa, la borramos de S3 para no acumular basura
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

    // BORRAR SCRIPT Y SU ASSET EN S3
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

    // FUNCIÓN AUXILIAR PRIVADA PARA LIMPIAR OBJETOS EN S3
    private function deleteFromS3($full_url) {
        // Extraemos la ruta interna (Key) quitando el dominio de Amazon (ej: uploads/archivo.jpg)
        $key = parse_url($full_url, PHP_URL_PATH);
        $key = ltrim($key, '/'); 
        
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key
            ]);
        } catch (S3Exception $e) {
            error_log("Error al eliminar objeto de S3: " . $e->getMessage());
        }
    }
}
?>