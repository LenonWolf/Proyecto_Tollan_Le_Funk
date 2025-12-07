// Verificar que el formulario existe antes de agregar el listener
const formBorrar = document.getElementById('form-borrar');

if (!formBorrar) {
    console.error('ERROR: No se encontró el formulario form-borrar');
} else {
    console.log('✓ Formulario form-borrar encontrado, registrando listener...');
    
    // Interceptar el envío del formulario de borrado
    formBorrar.addEventListener('submit', async function(e) {
        // Prevenir el comportamiento por defecto del navegador
        e.preventDefault();
        
        console.log('=== INICIANDO PROCESO DE BORRADO ===');

        // Mostrar confirmación al usuario antes de borrar
        if (!confirm("¿Seguro que deseas eliminar esta partida? Esta acción no se puede deshacer.")) {
            console.log('Usuario canceló el borrado');
            return;
        }

        // Construir FormData con los datos del formulario
        const formData = new FormData(this);
        console.log('ID a borrar:', formData.get('id'));

        try {
            // Obtener la ruta del directorio actual de la página
            const currentPagePath = window.location.pathname;
            const directory = currentPagePath.substring(0, currentPagePath.lastIndexOf('/') + 1);
            
            const deleteUrl = directory + 'delete_partida.php';  // <-- cambio temporal
            
            console.log('📍 Página actual:', currentPagePath);
            console.log('📁 Directorio:', directory);
            console.log('🎯 URL de borrado:', deleteUrl);
            
            // Realizar la petición
            console.log('Enviando petición DELETE...');
            const resp = await fetch(deleteUrl, {
                method: 'POST',
                body: formData
            });

            console.log('📊 Response status:', resp.status);
            console.log('📊 Response OK:', resp.ok);
            console.log('📊 Response URL:', resp.url);

            if (resp.ok) {
                console.log('✅ Partida eliminada exitosamente');
                const data = await resp.json();
                console.log('Respuesta del servidor:', data);
                
                // Recargar ventana padre si existe
                if (window.opener && !window.opener.closed) {
                    console.log('Recargando ventana padre...');
                    window.opener.location.reload();
                }
                
                // Cerrar ventana actual
                console.log('Cerrando ventana...');
                window.close();
            } else {
                // Error del servidor
                const errorText = await resp.text();
                console.error('❌ Error del servidor (código ' + resp.status + '):', errorText);
                alert("Error al eliminar la partida. Código: " + resp.status + "\nDetalles en consola (F12)");
            }
        } catch (err) {
            // Error de red
            console.error('❌ Error de red:', err);
            console.error('Tipo de error:', err.name);
            console.error('Mensaje:', err.message);
            alert("Error de red al eliminar la partida: " + err.message);
        }
    });
    
    console.log('✓ Listener registrado correctamente');
}