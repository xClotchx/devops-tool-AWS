<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('session.save_path', '/tmp'); 
session_start(); 

require_once "controllers/ScriptController.php";
require_once "controllers/AuthController.php";

// REGLA DE ORO: Si entran sin acción en la URL, SIEMPRE los mandamos a 'home' de forma estricta.
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

$authController = new AuthController();
$scriptController = new ScriptController();

// 1. RUTAS PÚBLICAS (Cualquiera puede entrar, y 'home' es la bienvenida garantizada)
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
// Si quieren ir a una acción privada (como index, edit, etc.) pero NO están logueados, al Login de cabeza.
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
        // Si ponen una acción que no existe en la URL, al Home por seguridad
        header("Location: index.php?action=home");
        break;
}
?>