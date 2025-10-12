document.getElementById('form-borrar').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!confirm("¿Seguro que deseas eliminar esta partida? Esta acción no se puede deshacer.")) {
        return;
    }

    const formData = new FormData(this);

    try {
        const resp = await fetch('delete_partida.php', {
            method: 'POST',
            body: formData
        });

        if (resp.ok) {
            // Avisar al padre que refresque (si existe)
            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
            }
            // Cerrar la ventana emergente
            window.close();
        } else {
            alert("Error al eliminar la partida.");
        }
    } catch (err) {
        console.error(err);
        alert("Error de red al eliminar la partida.");
    }
});