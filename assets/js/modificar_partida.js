// Interceptar el envío del formulario de borrado, confirmar con el usuario, realizar la petición POST al backend y gestionar la actualización de la interfaz (refresco/cierre).

// 1) Registrar un listener para el evento 'submit' del formulario de borrado.
//    - Se usa el ID 'form-borrar' definido en modificar_partida.php.
//    - Al interceptar el submit, podemos hacer una petición asíncrona en lugar de
//      enviar el formulario de manera tradicional (navegación completa).
document.getElementById('form-borrar').addEventListener('submit', async function(e) {
    // 2) Prevenir el comportamiento por defecto del navegador
    //    (evita la recarga completa de la página al enviar el formulario).
    e.preventDefault();

    // 3) Mostrar confirmación al usuario antes de borrar:
    //    - Confirm explica que la acción es irreversible.
    //    - Si el usuario cancela, simplemente retornamos y no se ejecuta el borrado.
    if (!confirm("¿Seguro que deseas eliminar esta partida? Esta acción no se puede deshacer.")) {
        return; // Usuario canceló: no continuamos
    }

    // 4) Construir un objeto FormData a partir del formulario actual:
    //    - FormData serializa los campos del formulario (incluye el input hidden 'id').
    //    - Es ideal para enviar datos vía fetch con método POST.
    const formData = new FormData(this);

    try {
        // 5) Realizar la petición HTTP al endpoint de borrado:
        //    - URL corregida para Azure: desde /src/modificar_partida.php llamamos a delete_partida.php
        //    - Si estamos en local (XAMPP), la ruta también funciona correctamente
        //    - Método POST: envía el 'id' de la partida para eliminarla.
        //    - 'body: formData' adjunta los datos del formulario.
        
        // Detectar la ruta base correcta
        const basePath = window.location.pathname.includes('/src/') 
            ? window.location.pathname.substring(0, window.location.pathname.indexOf('/src/') + 5)
            : '/src/';
        
        const resp = await fetch(basePath + 'delete_partida.php', {
            method: 'POST',
            body: formData
        });

        // 6) Evaluar la respuesta del servidor:
        //    - resp.ok es true para códigos 2xx (ej. 200 OK).
        //    - Si es OK: refrescar la ventana padre (si existe) y cerrar este pop-up.
        if (resp.ok) {
            // 6.1) Si esta ventana fue abierta por otra (window.opener), pedirle que recargue:
            //      - Esto actualiza la lista o el detalle de partidas en el editor padre.
            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
            }
            // 6.2) Cerrar la ventana emergente actual (modificar_partida.php):
            window.close();
        } else {
            // 6.3) Si el servidor responde con error (400/500), informar al usuario:
            alert("Error al eliminar la partida.");
        }
    } catch (err) {
        // 7) Manejo de errores de red:
        //    - Si la petición falla por conectividad, CORS, tiempo de espera, etc.
        //    - Se registra en consola para depuración y se muestra un mensaje al usuario.
        console.error(err);
        alert("Error de red al eliminar la partida.");
    }
});