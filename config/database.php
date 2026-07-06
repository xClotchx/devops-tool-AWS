<?php
    class Database {
        private $host;
        private $db_name;
        private $username; 
        private $password;
        public $conn;

        public function __construct() {
            // Intenta leer las variables de Docker, si no existen usa los datos locales por defecto
            $this->host = getenv('DB_HOST') ?: "host.docker.internal";
            $this->db_name = getenv('DB_NAME') ?: "devops_tool_db";
            $this->username = getenv('DB_USER') ?: "administra";
            $this->password = getenv('DB_PASSWORD') ?: "12345";
        }

        public function getConnection() {
            $this->conn = null;
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                    $this->username, 
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch(PDOException $exception) {
                echo "Error de conexion: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }
?>
