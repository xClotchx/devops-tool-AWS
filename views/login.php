<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DevOps Central</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="login-wrapper">
        
        <div class="login-box">
            
            <header class="login-header">
                <h1 class="login-title">DevOps Central</h1>
                <p class="login-subtitle">> Introduce tus credenciales para acceder al repositorio</p>
            </header>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                <div class="error-alert">
                    <p>[!] Usuario o contraseña incorrectos.</p>
                </div>
            <?php endif; ?>

            <form action="index.php?action=login_process" method="POST" class="script-form">
                <a href="index.php?action=home" ><-Inicio</a>
                <div class="form-group">
                    <label for="username">Usuario de Terminal</label>
                    <input type="text" name="username" id="username" placeholder="Ej: admin" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña de Acceso</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary login-btn">Iniciar Sesión</button>
                
                <div class="margin-top-file register-link-container">
                    <a href="index.php?action=register" class="btn-danger login-btn-secondary">[+] Registrarse</a>
                </div>
            </form>
            
        </div>
    </div>
</body>
</html>