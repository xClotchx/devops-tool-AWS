<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevOps Script Central - Panel</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <header class="main-header">
        <h1>DevOps Script Central</h1>
        <p>Tu repositorio de automatizaciones, bienvenido: <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Operador') ?></strong></p>
        <div class="header-actions">
            <a href="index.php?action=create" class="btn-primary">Añadir Nuevo Script</a>
            <a href="index.php?action=logout" class="btn-danger">Cerrar Sesión</a>
        </div>
    </header>

    <main>
        
        <div class="search-bar-container" style="margin-bottom: 30px;">
            <form action="index.php" method="GET" id="filter-form" class="script-form" style="padding: 15px 20px; display: flex; align-items: center; gap: 15px;">
                <input type="hidden" name="action" value="index">
                
                <label for="category_filter" style="margin-bottom: 0; white-space: nowrap; font-family: monospace; color: var(--accent); font-weight: bold;">
                    [i] Filtrar por categoría:
                </label>
                
                <select name="category_filter" id="category_filter" onchange="document.getElementById('filter-form').submit();" style="max-width: 400px; padding: 10px 40px 10px 14px;">
                    <option value="0">--- Mostrar todos los scripts ---</option>
                    <optgroup label="DevOps & Sistemas">
                        <option value="1" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 1) echo 'selected'; ?>>Configuración de Entorno</option>
                        <option value="2" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 2) echo 'selected'; ?>>Seguridad y Pentesting</option>
                        <option value="3" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 3) echo 'selected'; ?>>Respaldos y Bases de Datos</option>
                        <option value="4" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 4) echo 'selected'; ?>>Automatización / Bash</option>
                        <option value="12" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 12) echo 'selected'; ?>>Contenedores - Docker</option>
                    </optgroup>
                    <optgroup label="Lenguajes de Programación">
                        <option value="5" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 5) echo 'selected'; ?>>Desarrollo - PHP</option>
                        <option value="6" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 6) echo 'selected'; ?>>Desarrollo - Python</option>
                        <option value="7" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 7) echo 'selected'; ?>>Desarrollo - JavaScript / Node.js</option>
                        <option value="10" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 10) echo 'selected'; ?>>Desarrollo - Java</option>
                        <option value="11" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 11) echo 'selected'; ?>>Desarrollo - C++</option>
                        <option value="8" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 8) echo 'selected'; ?>>Frontend - HTML / CSS</option>
                        <option value="9" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 9) echo 'selected'; ?>>Consultas SQL / MariaDB</option>
                    </optgroup>
                    <optgroup label="IDEs & Entornos Mac">
                        <option value="13" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 13) echo 'selected'; ?>>IDE - Eclipse</option>
                        <option value="14" <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] == 14) echo 'selected'; ?>>IDE - Xcode</option>
                    </optgroup>
                </select>

                <?php if(isset($_GET['category_filter']) && $_GET['category_filter'] > 0): ?>
                    <a href="index.php?action=index" class="btn-secondary" style="font-size: 0.85rem; padding: 8px 14px; text-decoration: none;">Limpiar filtro</a>
                <?php endif; ?>
            </form>
        </div>

        <section class="grid-layout">
            <?php if (!empty($scripts) && is_array($scripts)): ?>
                <?php foreach ($scripts as $script): ?>
                    <article class="script-card">
                        <div class="card-header">
                            <span class="badge"><?= htmlspecialchars($script['category_name'] ?? 'General') ?></span>
                            <h2><?= htmlspecialchars($script['title'] ?? '') ?></h2>
                        </div>
                        
                        <p class="description"><?= htmlspecialchars($script['description'] ?? '') ?></p>
                        
                        <div class="terminal-box">
                            <pre><code><?= htmlspecialchars($script['code_content'] ?? '') ?></code></pre>
                            <div class="card-actions" style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end;">
                                <a href="index.php?action=edit&id=<?= $script['id'] ?>" class="btn-secondary">Editar</a>
                                <a href="index.php?action=delete&id=<?= $script['id'] ?>" class="btn-danger" onclick="return confirm('¿Seguro que quieres borrar este script?');">Borrar</a>
                            </div>
                        </div>

                        <?php if (!empty($script['instructions'])): ?>
                            <div class="instructions-box">
                                <strong>Instrucciones de uso:</strong>
                                <p><?= htmlspecialchars($script['instructions']) ?></p>
                            </div>
                        <?php endif; ?>

                       <?php if (!empty($script['image_path'])): ?>
    <div class="script-screenshot" style="margin-top: 20px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border);">
        <span style="display: block; background: #05070a; padding: 8px 15px; font-family: monospace; font-size: 0.8rem; color: var(--accent); border-bottom: 1px solid rgba(102, 252, 241, 0.15);">
            🌍 Captura en AWS S3:
        </span>
        <img src="https://devopsuploads.s3.amazonaws.com/<?= htmlspecialchars($script['image_path']) ?>" alt="Captura del código" style="width: 100%; display: block; height: auto;">
    </div>
<?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No hay scripts registrados todavía en esta categoría o espacio de trabajo. ¡Comienza agregando uno nuevo!</p>
                    <br>
                    <a href="index.php?action=create" class="btn-primary">Agregar mi primer script</a>
                </div>
            <?php endif; ?>
        </section>

    </main>
<script src="assets/js/main.js"></script>
</body>
</html>