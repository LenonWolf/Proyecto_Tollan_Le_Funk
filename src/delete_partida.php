<?php
include 'conexion.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    http_response_code(200); // éxito
} else {
    http_response_code(400); // petición inválida
}
?>