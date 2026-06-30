<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | DevOps Central</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container login-container">
        <header class="main-header login-header">
            <h1>Crear Cuenta</h1>
            <p>Registra un nuevo operador en el sistema local</p>
        </header>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-alert">
                <?php 
                    if ($_GET['error'] == 'password_mismatch') {
                        echo "[!] Las contraseñas no coinciden. Verifica e intenta de nuevo.";
                    } elseif ($_GET['error'] == 'failed') {
                        echo "[!] El nombre de usuario o correo electrónico ya están registrados.";
                    } else {
                        echo "[!] Ocurrió un error inesperado.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=register_process" method="POST" class="script-form">
            <div class="form-group">
                <label for="username">Nombre de Usuario</label>
                <input type="text" name="username" id="username" placeholder="Ej: jsmith" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico (Para Alertas)</label>
                <input type="email" name="email" id="email" placeholder="operador@dominio.com" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Contraseña de Terminal</label>
                <input type="password" name="password" id="password" placeholder="••••••••" autocomplete="new-password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn-primary login-btn">Registrar y Crear Cuenta</button>
            
            <div class="margin-top-file">
               <a href="index.php?action=login" class="btn-danger login-btn-secondary">← Volver al Login</a>
            </div>
        </form>
    </div>
</body>
</html>