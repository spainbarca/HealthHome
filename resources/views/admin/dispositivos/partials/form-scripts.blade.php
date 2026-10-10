<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputImagen = document.getElementById('imagen');
        const img = document.getElementById('vista-previa-imagen');
        const contenedorSinImagen = document.getElementById('imagen-vacia');
        const iconoImagen = document.getElementById('vista-previa-icono-imagen');
        const textoSinImagen = document.getElementById('imagen-sin-icono');
        const campoIcono = document.getElementById('icono');
        const iconoVista = document.getElementById('vista-previa-icono');
        const nombreVista = document.getElementById('nombre-vista-previa-icono');
        const eliminarImagen = document.getElementById('eliminar_imagen');
        const imagenOriginal = img.getAttribute('src');
        let objectUrl = null;

        // Health Icons funciona con clases CSS: healthicons-<filename>.
        // Se guarda únicamente el filename; no se inserta ningún SVG.
        function filenameValido(value) {
            const valueClean = value.trim().replace(/^healthicons-/i, '').toLowerCase();
            return /^[a-z0-9][a-z0-9_-]*$/.test(valueClean) ? valueClean : '';
        }

        function actualizarIcono() {
            const filename = filenameValido(campoIcono.value);
            const claseIcono = filename ? 'healthicons-' + filename : '';

            iconoVista.className = 'text-primary' + (claseIcono ? ' ' + claseIcono : '');
            iconoVista.hidden = !filename;
            iconoImagen.className = 'icono-vacio' + (claseIcono ? ' ' + claseIcono : '');
            iconoImagen.hidden = !filename;
            textoSinImagen.hidden = !!filename;

            nombreVista.textContent = filename
                ? claseIcono
                : (campoIcono.value.trim()
                    ? 'Filename inválido: utiliza letras, números, guiones o guiones bajos.'
                    : 'Escribe el filename para ver el icono');
        }

        function actualizarImagen() {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }

            const archivo = inputImagen.files && inputImagen.files[0];
            if (archivo && archivo.type.startsWith('image/')) {
                objectUrl = URL.createObjectURL(archivo);
                img.src = objectUrl;
                img.style.display = '';
                contenedorSinImagen.style.display = 'none';
            } else if (imagenOriginal && !(eliminarImagen && eliminarImagen.checked)) {
                img.src = imagenOriginal;
                img.style.display = '';
                contenedorSinImagen.style.display = 'none';
            } else {
                img.removeAttribute('src');
                img.style.display = 'none';
                contenedorSinImagen.style.display = '';
            }
        }

        campoIcono.addEventListener('input', actualizarIcono);
        inputImagen.addEventListener('change', actualizarImagen);
        if (eliminarImagen) eliminarImagen.addEventListener('change', actualizarImagen);

        actualizarIcono();
        actualizarImagen();
    });
</script>
