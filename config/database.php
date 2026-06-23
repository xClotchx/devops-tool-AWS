<?php
    class Database {
        // CAMBIO: Usamos localhost o 127.0.0.1 para referirse a la DB instalada en la misma instancia EC2
        private $host = "127.0.0.1"; 
        
        // Mantén el nombre de tu base de datos tal cual la configuraste en tu servidor MySQL local
        private $db_name = "devops_tool_db"; 
        
        // CAMBIO: Asegúrate de usar el usuario y contraseña que definiste en tu servidor MySQL de la EC2
        private $username = "administra"; 
        private $password = "12345";
        
        public $conn;

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
                // Es recomendable loguear esto en lugar de imprimirlo directamente en producción
                error_log("Error de conexión: " . $exception->getMessage());
                echo "Error de conexión a la base de datos.";
            }
            return $this->conn;
        }
    }
?>