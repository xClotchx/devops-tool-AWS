<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevOps Script Central | Repositorio de Automatización</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    

    <div class="landing-wrapper-custom">
        
        <header class="hero-section">
            <div class="logo-terminal-icon">&gt;_</div>
            
            <h1 class="hero-title">DevOps Script Central</h1>
            <p class="hero-subtitle">> Plataforma centralizada para despliegues y automatización de servidores</p>
            
            <div class="nav-buttons-group">
                <a href="index.php?action=login" class="btn-primary btn-cta">Ingresar al Sistema</a>
                <a href="index.php?action=register" class="btn-danger btn-cta">[+] Registrarse</a>
            </div>
        </header>

        <section class="info-grid-layout">
            <div class="info-card-box">
                <h3>¿Qué somos?</h3>
                <p>Somos un repositorio inteligente y privado diseñado para Ingenieros DevOps, Sysadmins y Desarrolladores. Centralizamos tus fragmentos de código críticos de infraestructura para evitar la pérdida de conocimiento en tus entornos locales.</p>
            </div>
            
            <div class="info-card-box">
                <h3>¿Qué hacemos?</h3>
                <p>Permitimos almacenar, clasificar por entornos (Docker, Bases de Datos, Seguridad, Cloud) y documentar scripts automatizados en Bash, Python o comandos de terminal. Todo bajo un acceso controlado, estructurado y multiusuario.</p>
            </div>
        </section>

        <section class="showcase-container">
            <div class="showcase-header-text">
                <h2>Diseñado para la Terminal</h2>
                <p>Visualiza tus configuraciones de forma limpia con sintaxis resaltada e instrucciones detalladas por cada despliegue.</p>
            </div>
            
            <div class="mock-terminal-window">
                <div class="terminal-window-bar">
                    <div class="window-dot dot-close"></div>
                    <div class="window-dot dot-minimize"></div>
                    <div class="window-dot dot-maximize"></div>
                    <div class="terminal-window-title">backup_mariadb_s3.sh</div>
                </div>
                <pre><code><span class="code-comment">#!/bin/bash</span>
<span class="code-comment"># Automatización de respaldos hacia contenedores en la nube</span>
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/db"
DB_NAME="devops_tool_db"

echo <span class="code-string">"Starting backup process for $DB_NAME..."</span>
mysqldump -u root -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql.gz"

<span class="code-keyword">if</span> [ $? -eq 0 ]; <span class="code-keyword">then</span>
    echo <span class="code-string">"[SUCCESS] Backup local completo. Sincronizando repositorio..."</span>
<span class="code-keyword">else</span>
    echo <span class="code-error">"[ERROR] Fallo crítico en el volcado de base de datos."</span>
<span class="code-keyword">fi</span></code></pre>
            </div>
        </section>

    </div>

</body>
</html>