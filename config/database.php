<?php
    class Database {
        private $host; 
        private $db_name; 
        private $username; 
        private $password;
        public $conn;

        public function __construct() {
            // Docker pasará estas variables dinámicamente; si no existen, usa los defaults de la derecha
            $this->host     = getenv('DB_HOST')     ?: "127.0.0.1";
            $this->db_name  = getenv('DB_NAME')     ?: "devops_tool_db";
            $this->username = getenv('DB_USER')     ?: "administra";
            $this->password = getenv('DB_PASSWORD') ?: "12345";
        }

        public function getConnection() {
            $this->conn = null;
            try{
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
                error_log("Error de conexión: " . $exception->getMessage());
                echo "Error de conexión a la base de datos.";
            }
            return $this->conn;
        }
    }
?>