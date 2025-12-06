<?php
require_once '../conexion.php';

$db = new Conexion('usr_lector', 'lector123');
$conn = $db->conectar();

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre ASC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar DMs'
    ]);
    $db->cerrar();
    exit;
}

$dms = [];

while ($row = $result->fetch_assoc()) {
    $dms[] = [
        'id' => $row['ID_DM'],
        'nombre' => $row['Nombre']
    ];
}

echo json_encode([
    'success' => true,
    'dms' => $dms
]);

$db->cerrar();