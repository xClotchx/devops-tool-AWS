<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Script | DevOps Central</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container" style="max-width: 750px; margin: 0 auto; padding: 20px;">
        <header class="main-header">
            <div>
                <h1>Añadir Nuevo Script</h1>
                <p>Ingresa un comando o fragmento útil en tu repositorio</p>
            </div>
            <a href="index.php" class="btn-secondary">← Volver al listado</a>
        </header>

        <form action="index.php?action=store" method="POST" enctype="multipart/form-data" class="script-form">
            <div class="form-group">
                <label for="title">Título del Script o Comando</label>
                <input type="text" name="title" id="title" placeholder="Ej: Clonar Base de Datos Remota" required>
            </div>

            <div class="form-group">
                <label for="category_id">Categoría / Lenguaje / Herramienta</label>
                <select name="category_id" id="category_id" required>
                    <optgroup label="DevOps & Sistemas">
                        <option value="1">Configuración de Entorno</option>
                        <option value="2">Seguridad y Pentesting</option>
                        <option value="3">Respaldos y Bases de Datos</option>
                        <option value="4">Automatización / Bash</option>
                        <option value="12">Contenedores - Docker</option>
                    </optgroup>
                    <optgroup label="Lenguajes de Programación">
                        <option value="5">Desarrollo - PHP</option>
                        <option value="6">Desarrollo - Python</option>
                        <option value="7">Desarrollo - JavaScript / Node.js</option>
                        <option value="10">Desarrollo - Java</option>
                        <option value="11">Desarrollo - C++</option>
                        <option value="8">Frontend - HTML / CSS</option>
                        <option value="9">Consultas SQL / MariaDB</option>
                    </optgroup>
                    <optgroup label="IDEs & Entornos Mac">
                        <option value="13">IDE - Eclipse</option>
                        <option value="14">IDE - Xcode</option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label for="description">¿Para qué sirve?</label>
                <textarea name="description" id="description" placeholder="Breve descripción del funcionamiento del script..."></textarea>
            </div>

            <div class="form-group">
                <label for="code_content">Código / Script</label>
                <textarea name="code_content" id="code_content" style="font-family: monospace;" placeholder="Pega tu comando o bloque de código aquí..." required></textarea>
            </div>

            <div class="form-group">
                <label for="instructions">Instrucciones de ejecución</label>
                <input type="text" name="instructions" id="instructions" placeholder="Ej: chmod +x script.sh && ./script.sh">
            </div>

            <div class="form-group">
                <label for="script_image">[FOTO] Subir captura de pantalla del código / resultado</label>
                <input type="file" name="script_image" id="script_image" accept="image/*" style="padding: 10px; background: var(--input-bg); border: 1px solid var(--border); width: 100%; color: white; border-radius: 8px;">
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 20px;">Guardar Script</button>
        </form>
    </div>
</body>
</html>