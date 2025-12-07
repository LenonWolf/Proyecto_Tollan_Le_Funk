<?php
session_name('TOLLAN_SESSION');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['ID_Usuarios'])) {
    http_response_code(401);
    die(json_encode(['error' => 'No autenticado']));
}

if (!in_array($_SESSION['Tipo_Usr'], ['Adm', 'Mod'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Sin permisos']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Método no permitido']));
}

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    die(json_encode(['error' => 'ID inválido']));
}

require_once 'conexion.php';
$db = new Conexion('usr_del_partida', 'del_partida123');
$conn = $db->conectar();

$stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$db->cerrar();

echo json_encode(['success' => true, 'affected' => $affected]);