<?php
// API para obtener la lista de todos los sistemas disponibles

require_once '../conexion.php';

// Crear conexión con usuario de solo lectura
$db = new Conexion('usr_lector', 'lector123');
$conn = $db->conectar();


header('Content-Type: application/json; charset=utf-8'); // Respuesta en formato JSON

/***************************
* CONSULTA DE SISTEMAS *
***************************/

$sql = "SELECT ID_Sistema, Titulo FROM sistema ORDER BY Titulo ASC";

$result = $conn->query($sql);

// Verificar si hubo error en la consulta
if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al consultar sistemas'
    ]);
    exit;
}

$sistemas = [];

// Recorrer todos los sistemas y agregarlos al array
while ($row = $result->fetch_assoc()) {
    $sistemas[] = [
        'id' => $row['ID_Sistema'],
        'titulo' => $row['Titulo']
    ];
}

/*****************
* RESPUESTA JSON *
*****************/

echo json_encode([
    'success' => true,
    'sistemas' => $sistemas
]);