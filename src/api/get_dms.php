<?php
// API para obtener la lista de todos los Dungeon Masters disponibles

include '../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre ASC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar DMs'
    ]);
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