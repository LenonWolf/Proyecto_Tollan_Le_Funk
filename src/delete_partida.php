<?php
// Iniciar sesión
session_name('TOLLAN_SESSION');
session_start();

// Header JSON
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['ID_Usuarios'])) {
    http_response_code(401);
    die(json_encode(['error' => 'No autenticado']));
}

// Verificar permisos
if (!in_array($_SESSION['Tipo_Usr'], ['Adm', 'Mod'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Sin permisos']));
}

// Verificar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Método no permitido']));
}

// Obtener ID
$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    die(json_encode(['error' => 'ID inválido']));
}

// Conectar a BD
require_once 'conexion.php';
$db = new Conexion('usr_del_partida', 'del_partida123');
$conn = $db->conectar();

// Ejecutar DELETE
$stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$db->cerrar();

// Respuesta
echo json_encode(['success' => true, 'affected' => $affected]);
?>