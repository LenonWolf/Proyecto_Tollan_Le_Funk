// Detectar ruta base del proyecto
function getBasePath() {
    const hostname = window.location.hostname;
    return hostname.includes('azurewebsites.net') ? '/' : '/Tollan_Le_Funk/';
}

function url(path) {
    path = path.replace(/^\/+/, '');
    return getBasePath() + path;
}

document.getElementById('form-borrar').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!confirm("¿Seguro que deseas eliminar esta partida? Esta acción no se puede deshacer.")) {
        return;
    }

    const formData = new FormData(this);

    try {
        // Usar ruta dinámica en lugar de relativa
        const deleteUrl = url('src/delete_partida.php');
        console.log('Eliminando partida en:', deleteUrl); // Debug
        
        const resp = await fetch(deleteUrl, {
            method: 'POST',
            body: formData
        });

        console.log('Respuesta del servidor:', resp.status); // Debug

        if (resp.ok) {
            if (window.opener && !window.opener.closed) {
                window.opener.location.reload();
            }
            window.close();
        } else {
            const errorText = await resp.text();
            console.error('Error del servidor:', errorText);
            alert("Error al eliminar la partida. Código: " + resp.status);
        }
    } catch (err) {
        console.error('Error de red:', err);
        alert("Error de red al eliminar la partida: " + err.message);
    }
});