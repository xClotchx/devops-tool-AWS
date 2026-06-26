<?php /** @var array $script */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Script | DevOps Central</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <div>
                <h1>Editar Script</h1>
                <p>Corrigiendo el registro seleccionado</p>
            </div>
            <a href="dashboard.php" class="btn-secondary">← Cancelar</a>
        </header>

        <form action="index.php?action=update&id=<?php echo $script['id']; ?>" method="POST" enctype="multipart/form-data" class="script-form">
            
            <div class="form-group">
                <label for="title">Título del Script o Comando</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($script['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Categoría / Lenguaje</label>
                <select name="category_id" id="category_id" required>
                    <optgroup label="DevOps & Sistemas">
                        <option value="1" <?php if($script['category_id'] == 1) { echo 'selected'; } ?>>Configuración de Entorno</option>
                        <option value="2" <?php if($script['category_id'] == 2) { echo 'selected'; } ?>>Seguridad y Pentesting</option>
                        <option value="3" <?php if($script['category_id'] == 3) { echo 'selected'; } ?>>Respaldos y Bases de Datos</option>
                        <option value="4" <?php if($script['category_id'] == 4) { echo 'selected'; } ?>>Automatización / Bash</option>
                        <option value="12" <?php if($script['category_id'] == 12) { echo 'selected'; } ?>>Contenedores - Docker</option>
                    </optgroup>
                    <optgroup label="Lenguajes de Programación">
                        <option value="5" <?php if($script['category_id'] == 5) { echo 'selected'; } ?>>Desarrollo - PHP</option>
                        <option value="6" <?php if($script['category_id'] == 6) { echo 'selected'; } ?>>Desarrollo - Python</option>
                        <option value="7" <?php if($script['category_id'] == 7) { echo 'selected'; } ?>>Desarrollo - JavaScript / Node.js</option>
                        <option value="10" <?php if($script['category_id'] == 10) { echo 'selected'; } ?>>Desarrollo - Java</option>
                        <option value="11" <?php if($script['category_id'] == 11) { echo 'selected'; } ?>>Desarrollo - C++</option>
                        <option value="8" <?php if($script['category_id'] == 8) { echo 'selected'; } ?>>Frontend - HTML / CSS</option>
                        <option value="9" <?php if($script['category_id'] == 9) { echo 'selected'; } ?>>Consultas SQL / MariaDB</option>
                    </optgroup>
                    <optgroup label="IDEs & Entornos Mac">
                        <option value="13" <?php if($script['category_id'] == 13) { echo 'selected'; } ?>>IDE - Eclipse</option>
                        <option value="14" <?php if($script['category_id'] == 14) { echo 'selected'; } ?>>IDE - Xcode</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label for="description">¿Para qué sirve?</label>
                <textarea name="description" id="description"><?php echo htmlspecialchars($script['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="code_content">Código / Script</label>
                <textarea name="code_content" id="code_content" class="code-area" required><?php echo $script['code_content']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="instructions">Instrucciones de ejecución</label>
                <input type="text" name="instructions" id="instructions" value="<?php echo htmlspecialchars($script['instructions']); ?>">
            </div>

            <?php if (!empty($script['image_path'])): ?>
                <div class="form-group current-image-box">
                    <label class="danger-label">Captura actual asignada:</label>
                    <img src="assets/uploads/<?php echo $script['image_path']; ?>" class="preview-thumb">
                    
                    <label class="checkbox-container">
                        <input type="checkbox" name="delete_image" value="1"> 
                        <span class="warning-text">[!] Marcar para eliminar esta captura por completo</span>
                    </label>
                </div>
            <?php endif; ?>

            <div class="form-group margin-top-file">
                <label for="script_image">[FOTO] Reemplazar o subir captura de pantalla</label>
                <div class="file-input-wrapper">
                    <input type="file" name="script_image" id="script_image" accept="image/*">
                    <button type="button" id="clear_image_btn" class="btn-danger hidden-element">Quitar</button>
                </div>
            </div>

            <button type="submit" class="btn-primary submit-btn">Guardar Cambios</button>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('script_image');
        const clearBtn = document.getElementById('clear_image_btn');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                clearBtn.classList.remove('hidden-element');
            } else {
                clearBtn.classList.add('hidden-element');
            }
        });

        clearBtn.addEventListener('click', function() {
            fileInput.value = '';
            this.classList.add('hidden-element');
        });
    </script>

    <script src="/assets/js/main.js"></script>
</body>
</html>