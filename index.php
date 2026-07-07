<?php
// 1. El búfer de salida siempre va en la línea 1
ob_start(); 

// 2. Reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 3. Ajustes de la sesión (SIEMPRE antes de inicializarla)
ini_set('session.save_path', '/tmp'); 

// 4. Iniciamos la sesión de forma segura y una sola vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Carga de controladores
require_once "controllers/ScriptController.php";
require_once "controllers/AuthController.php";

// REGLA DE ORO: Si entran sin acción en la URL, SIEMPRE los mandamos a 'home' de forma estricta.
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

$authController = new AuthController();
$scriptController = new ScriptController();

// 1. RUTAS PÚBLICAS
if ($action == 'home') {
    require_once "views/home.php";
    exit();
} elseif ($action == 'login') {
    $authController->showLogin();
    exit();
} elseif ($action == 'login_process') {
    $authController->loginProcess();
    exit();
} elseif ($action == 'register') {
    $authController->showRegister();
    exit();
} elseif ($action == 'register_process') {
    $authController->registerProcess();
    exit();
}

// 2. FILTRO DE SEGURIDAD PARA RUTAS PRIVADAS
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// 3. RUTAS PRIVADAS (Solo si pasaron el login)
switch ($action) {
    case 'index':
        $scriptController->index();
        break;
    case 'create':
        require_once "views/create.php";
        break;
    case 'store':
        $scriptController->store();
        break;
    case 'edit':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $scriptController->edit($id);
        break;
    case 'update':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $scriptController->updateScript($id);
        break;
    case 'delete':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $scriptController->deleteScript($id);
        break;
    case 'logout':
        $authController->logout();
        break;
    default:
        header("Location: index.php?action=home");
        break;
}
?>