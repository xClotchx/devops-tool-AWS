<?php
require_once "models/script.php";
require_once "config/database.php"; 

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

        // Docker pasará estas variables de forma segura
        $this->bucket = $_ENV['AWS_BUCKET_NAME'] ?? getenv('AWS_BUCKET_NAME') ?: 'tu-bucket-name';
        
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['AWS_REGION'] ?? getenv('AWS_REGION') ?: 'us-east-1',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? getenv('AWS_ACCESS_KEY_ID'),
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    // LISTAR SCRIPT - Muestra la URL directa y pública
    public function index() {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        $category_filter = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : null;
        $stmt = $this->scriptModel->readAll($category_filter);
        $scripts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Adjuntamos la URL estática directa del Bucket público
        foreach ($scripts as &$script) {
            if (!empty($script['image_path'])) {
                $script['image_url_firmada'] = "https://{$this->bucket}.s3.amazonaws.com/" . $script['image_path'];
            } else {
                $script['image_url_firmada'] = null;
            }
        }
        unset($script);
        
        require_once "views/dashboard.php";
    }

    // GUARDAR EN S3
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
                    $this->s3->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $s3_key,
                        'SourceFile' => $file_tmp
                    ]);
                } catch (S3Exception $e) {
                    error_log("Error S3: " . $e->getMessage());
                    $s3_key = null;
                }
            }

            $this->scriptModel->image_path = $s3_key;

            if ($this->scriptModel->create()) {
                header("Location: index.php?action=index&success=created");
            } else {
                header("Location: index.php?action=index&error=failed");
            }
            exit();
        }
    }

    // MOSTRAR FORMULARIO EDICIÓN
    public function edit($id) {
        $this->scriptModel->user_id = $_SESSION['user_id'];
        $script = $this->scriptModel->readOne($id);
        if ($script) {
            if (!empty($script['image_path'])) {
                $script['image_url_firmada'] = "https://{$this->bucket}.s3.amazonaws.com/" . $script['image_path'];
            } else {
                $script['image_url_firmada'] = null;
            }
            require_once "views/edit.php";
        } else {
            header("Location: index.php?action=index");
        }
    }

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

            if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
                $this->scriptModel->image_path = null;
                if (!empty($currentScript['image_path'])) {
                    $this->deleteFromS3($currentScript['image_path']);
                }
            } elseif (isset($_FILES['script_image']) && $_FILES['script_image']['error'] == 0) {
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
                    if (!empty($currentScript['image_path'])) {
                        $this->deleteFromS3($currentScript['image_path']);
                    }
                } catch (S3Exception $e) {
                    $this->scriptModel->image_path = $currentScript['image_path'];
                }
            } else {
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

    private function deleteFromS3($s3_key) {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $s3_key
            ]);
        } catch (S3Exception $e) {
            error_log("Error al eliminar: " . $e->getMessage());
        }
    }
}
?>