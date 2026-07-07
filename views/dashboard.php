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
            <article class="script-card" style="border: 1px solid var(--border); padding: 20px; border-radius: 12px; background: #0c1017; margin-bottom: 20px;">
                
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 1.4rem; color: #fff;"><?= htmlspecialchars($script['title'] ?? '') ?></h2>
                    <span class="badge" style="background: rgba(102, 252, 241, 0.1); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                        <?= htmlspecialchars($script['category_name'] ?? 'General') ?>
                    </span>
                </div>
                
                <p class="description" style="color: #8b949e; font-size: 0.95rem; margin-bottom: 15px;"><?= htmlspecialchars($script['description'] ?? '') ?></p>
                
                <details style="background: #070a10; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden;">
                    <summary style="list-style: none; padding: 12px 15px; font-weight: bold; color: var(--accent); cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: #111622; user-select: none;">
                        <span>🔍 Ver detalles del Script</span>
                        <span style="font-size: 0.8rem; opacity: 0.7;">▼</span>
                    </summary>
                    
                    <div style="padding: 15px; border-top: 1px solid rgba(255,255,255,0.05);">
                        <div class="terminal-box" style="margin-bottom: 15px;">
                            <span style="display:block; font-size:0.8rem; color:#8b949e; margin-bottom:5px; font-family:monospace;">[Source Code]</span>
                            <pre style="margin: 0; background: #010409; padding: 12px; border-radius: 6px; overflow-x: auto;"><code style="font-family: monospace; color: #e6edf3; font-size: 0.9rem;"><?= htmlspecialchars($script['code_content'] ?? '') ?></code></pre>
                        </div>

                        <?php if (!empty($script['instructions'])): ?>
                            <div class="instructions-box" style="margin-bottom: 15px; background: rgba(255,255,255,0.02); padding: 12px; border-radius: 6px; border-left: 3px solid var(--accent);">
                                <strong style="color: #fff; font-size: 0.9rem; display: block; margin-bottom: 4px;">📖 Cómo usarlo:</strong>
                                <p style="color: #c9d1d9; margin: 0; font-size: 0.9rem;"><?= htmlspecialchars($script['instructions']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($script['image_url_firmada'])): ?>
                            <div class="script-screenshot" style="margin-top: 15px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); max-width: 450px; margin-left: auto; margin-right: auto;">
                                <span style="display: block; background: #05070a; padding: 6px 12px; font-family: monospace; font-size: 0.75rem; color: var(--accent); border-bottom: 1px solid rgba(102, 252, 241, 0.15);">
                                    🌍 Captura en AWS S3:
                                </span>
                                <img src="<?= $script['image_url_firmada'] ?>" alt="Captura del código" style="width: 100%; display: block; height: auto; max-height: 250px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>
                </details>

                <div class="card-actions" style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="index.php?action=edit&id=<?= $script['id'] ?>" class="btn-secondary" style="padding: 6px 12px; font-size: 0.85rem;">Editar</a>
                    <a href="index.php?action=delete&id=<?= $script['id'] ?>" class="btn-danger" style="padding: 6px 12px; font-size: 0.85rem;" onclick="return confirm('¿Seguro que quieres borrar este script?');">Borrar</a>
                </div>

            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>No hay scripts registrados todavía en esta categoría.</p>
            <br>
            <a href="index.php?action=create" class="btn-primary">Agregar mi primer script</a>
        </div>
    <?php endif; ?>
</section>


    </main>
<script src="assets/js/main.js"></script>
</body>
</html>