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

         <!-- CAMPO 1: CONTRASEÑA DE TERMINAL -->
<div class="form-group" style="margin-bottom: 20px;">
    <label style="display: block; font-family: monospace; color: var(--accent); margin-bottom: 8px;">Contraseña de Terminal</label>
    <input type="text" 
           name="term_key" 
           id="term_key"
           required 
           autocomplete="off"
           class="anti-safari-input"
           style="background: #111622; color: #fff; border: 1px solid var(--border); padding: 12px; border-radius: 8px; width: 100%; font-size: 1.1rem;">
</div>

<!-- CAMPO 2: CONFIRMAR CONTRASEÑA -->
<div class="form-group" style="margin-bottom: 20px;">
    <label style="display: block; font-family: monospace; color: var(--accent); margin-bottom: 8px;">Confirmar Contraseña</label>
    <input type="text" 
           name="confirm_term_key" 
           id="confirm_term_key"
           required 
           autocomplete="off"
           class="anti-safari-input"
           style="background: #111622; color: #fff; border: 1px solid var(--border); padding: 12px; border-radius: 8px; width: 100%; font-size: 1.1rem;">
</div>

<!-- ESTILOS INTERNOS DE CONTROL TOTAL -->


            <!-- Corrección de clases de los botones -->
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Registrar y Crear Cuenta</button>
            
            <div style="margin-top: 15px; text-align: center;">
                <a href="index.php?action=login" class="btn-danger" style="display: block; width: 100%; text-decoration: none;">← Volver al Login</a>
            </div>
        </form>
    </div>
</body>
</html>
