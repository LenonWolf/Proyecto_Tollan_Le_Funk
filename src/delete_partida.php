<?php
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']);

require_once 'conexion.php';

$db = new Conexion('usr_del_partida', 'del_partida123');
$conn = $db->conectar();

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $db->cerrar();
    
    http_response_code(200);
} else {
    $db->cerrar();
    http_response_code(400);
}