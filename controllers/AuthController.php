<?php
require_once "models/User.php";
require_once "config/database.php";

// Cargamos PHPMailer desde el autoloader de Composer
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function showLogin() {
        require_once "views/login.php";
    }

    // MOSTRAR FORMULARIO DE REGISTRO
    public function showRegister() {
        require_once "views/register.php";
    }

   // PROCESAR EL REGISTRO CON AUTOLOGIN AUTOMÁTICO
    public function registerProcess() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // CORRECCIÓN PARA SAFARI: Leemos los nuevos nombres de input ocultos
            $password = $_POST['term_key'] ?? '';
            $confirm_password = $_POST['confirm_term_key'] ?? '';

            if ($password !== $confirm_password) {
                header("Location: index.php?action=register&error=password_mismatch");
                exit();
            }

            $this->userModel->username = $_POST['username'];
            $this->userModel->email = $_POST['email'];
            $this->userModel->password = $password;

            if ($this->userModel->register()) {
                // Obtenemos los datos del usuario recién creado para levantar la sesión de inmediato
                $user = $this->userModel->login();
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    
                    // PASO DE DEPURACIÓN EN MAC: Comentamos temporalmente para evitar bloqueos de red en Docker
                    // $this->sendLoginNotification($user['username'], $user['email']);
                }

                // CORRECCIÓN: Lo mandamos explícitamente a la acción index privada
                header("Location: index.php?action=index");
            } else {
                header("Location: index.php?action=register&error=failed");
            }
            exit();
        }
    }

    // PROCESAR EL LOGIN + ENVIAR NOTIFICACIÓN
    public function loginProcess() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->userModel->username = $_POST['username'];
            $password_input = $_POST['password'];

            $user = $this->userModel->login();

            if ($user && password_verify($password_input, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // PASO DE DEPURACIÓN EN MAC: Comentamos temporalmente para evitar bloqueos de red en Docker
                // $this->sendLoginNotification($user['username'], $user['email']);

                // CORRECCIÓN: Lo mandamos explícitamente a la acción index privada
                header("Location: index.php?action=index");
            } else {
                header("Location: index.php?action=login&error=invalid");
            }
            exit();
        }
    }

    // FUNCIÓN DE NOTIFICACIÓN POR EMAIL
    private function sendLoginNotification($username, $user_email) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del Servidor SMTP (Ejemplo con Gmail)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'clotchproyectos@gmail.com'; 
            $mail->Password   = 'mknbuhhuiqgojwtr'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinatarios
            $mail->setFrom('clotchproyectos@gmail.com', 'DevOps Script Central');
            $mail->addAddress($user_email, $username); 

            // Contenido del correo en formato Terminal
            $mail->isHTML(true);
            $mail->Subject = '[ALERTA] Nuevo inicio de sesion detectado';
            
            $mail->Body    = "
                <div style='background: #0f111a; color: #66fcf1; padding: 20px; font-family: monospace; border-radius: 8px;'>
                    <h2 style='color: #ff4b4b; border-bottom: 1px solid rgba(255,75,75,0.3); padding-bottom: 10px;'>[ SYSTEM LOG: SECURITY ]</h2>
                    <p>Hola <strong>{$username}</strong>,</p>
                    <p>Se ha registrado un acceso exitoso a tu repositorio DevOps Script Central.</p>
                    <p>--------------------------------------------------</p>
                    <p><strong>Usuario:</strong> {$username}</p>
                    <p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>
                    <p><strong>IP de Origen:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>
                    <p>--------------------------------------------------</p>
                    <p style='color: #c5c6c7; font-size: 0.85rem;'>Si no fuiste tú, te sugerimos cambiar tus credenciales de inmediato desde tu servidor local.</p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("No se pudo enviar la alerta de login. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>