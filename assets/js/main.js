document.addEventListener('DOMContentLoaded', () => {
    // Buscar todos los bloques de código para añadirles un botón de "Copiar"
    const terminalBoxes = document.querySelectorAll('.terminal-box');

    terminalBoxes.forEach(box => {
        // Crear el botón de copiar
        const copyBtn = document.createElement('button');
        copyBtn.innerText = 'Copiar';
        copyBtn.className = 'btn-copy';
        box.style.position = 'relative';
        box.appendChild(copyBtn);

        // Lógica para copiar al portapapeles
        copyBtn.addEventListener('click', () => {
            const code = box.querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                copyBtn.innerText = '¡Copiado!';
                copyBtn.style.background = '#00ff66';
                copyBtn.style.color = '#000';
                
                // Restaurar el botón después de 2 segundos
                setTimeout(() => {
                    copyBtn.innerText = 'Copiar';
                    copyBtn.style.background = 'rgba(255,255,255,0.1)';
                    copyBtn.style.color = 'var(--text-muted)';
                }, 2000);
            });
        });
    });
});