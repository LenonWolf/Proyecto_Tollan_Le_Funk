<?php
// get_sistema.php
include "conexion.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success'=>false, 'message'=>'ID de sistema inválido']);
    exit;
}

$id = intval($_GET['id']);

// Info principal del sistema + tipo
$sql = "SELECT s.Descripcion, s.Clasificacion, t.Tipo
        FROM sistema s
        INNER JOIN tipo t ON s.ID_Tipo = t.ID_Tipo
        WHERE s.ID_Sistema = ?";
$stmt = $conn->prepare($sql);

if(!$stmt){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta principal']);
    exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success'=>false, 'message'=>'Sistema no encontrado']);
    $stmt->close();
    exit;
}

$row = $res->fetch_assoc();
$stmt->close();

$sql2 = "SELECT d.Dado 
         FROM dado d
         INNER JOIN dado_sistema ds ON d.ID_Dado = ds.ID_Dado
         WHERE ds.ID_Sistema = ?";
$stmt2 = $conn->prepare($sql2);
if(!$stmt2){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta de dado']);
    exit;
}
$stmt2->bind_param('i', $id);
$stmt2->execute();
$res2 = $stmt2->get_result();

$dado = [];
while($r = $res2->fetch_assoc()) {
    $dado[] = $r['Dado'];
}
$stmt2->close();

$sql3 = 'SELECT g.Genero 
         FROM genero g
         INNER JOIN genero_sistema gs ON g.ID_Genero = gs.ID_Genero
         WHERE gs.ID_Sistema = ?';

$stmt3 = $conn->prepare($sql3);
if(!$stmt3){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta de genero']);
    exit;
}
$stmt3->bind_param('i', $id);
$stmt3->execute();
$res3 = $stmt3->get_result();

$genero = [];
while($r = $res3->fetch_assoc()) {
    $genero[] = $r['Genero'];
}
$stmt3->close();

echo json_encode([
    'success' => true,
    'descripcion' => $row['Descripcion'],
    'clasificacion' => $row['Clasificacion'],
    'tipo' => $row['Tipo'],
    'genero' => $genero,
    'dado' => $dado
]);