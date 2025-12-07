<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    @session_name('TOLLAN_SESSION');
    session_start();
}

// Incluir archivos necesarios
require_once 'config.php';
require_once 'conexion.php';

// Establecer header JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar autenticación manual (sin check_auth.php para evitar redirecciones)
if (!isset($_SESSION['ID_Usuarios'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sesión no válida']);
    exit;
}

// Verificar permisos (solo Adm y Mod pueden eliminar partidas)
if (!in_array($_SESSION['Tipo_Usr'], ['Adm', 'Mod'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permisos para eliminar partidas']);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Conectar a la base de datos
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
    
    // Preparar y ejecutar la consulta de eliminación
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al preparar consulta: ' . $conn->error]);
        $db->cerrar();
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
        'message' => 'Partida eliminada correctamente',
        'affected_rows' => $affected
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
}
?>