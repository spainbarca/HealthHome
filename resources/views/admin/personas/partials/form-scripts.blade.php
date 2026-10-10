<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('user_id');
        if (!select) return;

        const nombre = document.getElementById('persona-nombre-preview');
        const email = document.getElementById('persona-email-preview');
        const avatar = document.getElementById('persona-avatar-preview');

        function actualizar() {
            const opcion = select.options[select.selectedIndex];
            const textoNombre = opcion?.dataset.nombre || 'Selecciona un usuario';
            nombre.textContent = textoNombre;
            email.textContent = opcion?.dataset.email || 'La ficha utilizará los datos de su cuenta.';

            const foto = opcion?.dataset.foto || '';
            let elemento;
            if (foto) {
                elemento = document.createElement('img');
                elemento.src = foto;
                elemento.alt = 'Fotografía del usuario seleccionado';
            } else {
                elemento = document.createElement('span');
                const partes = textoNombre.trim().split(/\s+/);
                elemento.textContent = opcion?.value
                    ? (partes[0]?.[0] || '') + (partes[1]?.[0] || '')
                    : 'P';
            }
            elemento.className = 'hh-persona-avatar lg';
            avatar.replaceChildren(elemento);
        }

        select.addEventListener('change', actualizar);
        actualizar();
    });
</script>
