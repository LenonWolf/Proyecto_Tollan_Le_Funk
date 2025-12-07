<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_name('TOLLAN_SESSION');
    session_start();
}

// Habilitar reporte de errores para depuración (comentar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar a la base de datos con usuario específico para eliminar partidas
require_once 'conexion.php';

try {
    $db = new Conexion('usr_del_partida', 'del_partida123');
    $conn = $db->conectar();
    
    // Obtener el ID de la partida desde POST
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID no proporcionado']);
        exit;
    }
    
    // Verificar que el ID sea numérico
    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }
    
    // Preparar y ejecutar la consulta de eliminación usando prepared statement
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al preparar consulta: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    
    if (!$success) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al ejecutar: ' . $stmt->error]);
        $stmt->close();
        $db->cerrar();
        exit;
    }
    
    $affected = $stmt->affected_rows;
    $stmt->close();
    $db->cerrar();
    
    // Responder con éxito
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Partida eliminada',
        'affected_rows' => $affected
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
}
?>