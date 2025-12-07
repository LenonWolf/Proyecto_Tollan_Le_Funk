<?php
// Incluir configuración de rutas (para compatibilidad Azure/local)
require_once 'config.php';

// Verificar autenticación y permisos
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']);

// Conectar a la base de datos con usuario específico para eliminar partidas
require_once 'conexion.php';

$db = new Conexion('usr_del_partida', 'del_partida123');
$conn = $db->conectar();

// Obtener el ID de la partida desde POST
$id = $_POST['id'] ?? null;

if ($id) {
    // Preparar y ejecutar la consulta de eliminación usando prepared statement
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $db->cerrar();
    
    // Responder con código 200 (OK)
    http_response_code(200);
} else {
    // Si no hay ID válido, cerrar conexión y responder con error 400
    $db->cerrar();
    http_response_code(400);
}
?>