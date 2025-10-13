<?php
// Recibir un ID por GET, consultar datos del sistema (descripción, clasificación, tipo, dados y géneros) y devolverlos como JSON.

include "conexion.php"; // Incluir la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8'); // Definir el encabezado HTTP para indicar que la respuesta será JSON (UTF-8)

/***************************
* VALIDACIÓN DE PARÁMETROS *
***************************/

// Verificar que exista el parámetro 'id' y que sea numérico.
// Si no cumple, responder con JSON de error y terminar ejecución.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success'=>false, 'message'=>'ID de sistema inválido']);
    exit;
}

$id = intval($_GET['id']); // Normalizar el ID a entero para evitar problemas de tipo

/*************************************
* CONSULTA PRINCIPAL: SISTEMA + TIPO *
*************************************/

// SQL para obtener la descripción y clasificación del sistema, además del nombre del tipo (JOIN entre 'sistema' y 'tipo').
$sql = "SELECT s.Descripcion, s.Clasificacion, t.Tipo
        FROM sistema s
        INNER JOIN tipo t ON s.ID_Tipo = t.ID_Tipo
        WHERE s.ID_Sistema = ?";

$stmt = $conn->prepare($sql); // Preparar el statement para evitar inyección SQL

// Si la preparación falla, responder con error y terminar
if(!$stmt){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta principal']);
    exit;
}

$stmt->bind_param('i', $id); // Asociar el parámetro 'id' (i = integer) y ejecutar la consulta
$stmt->execute();

$res = $stmt->get_result(); // Obtener el conjunto de resultados como objeto mysqli_result

// Si no hay filas, el sistema no existe; responder con error
if ($res->num_rows === 0) {
    echo json_encode(['success'=>false, 'message'=>'Sistema no encontrado']);
    $stmt->close(); // Cerrar el statement antes de salir
    exit;
}

$row = $res->fetch_assoc(); // Extraer la fila única con los datos del sistema
$stmt->close(); // Cerrar el statement de la consulta principal

/*****************************************
* CONSULTA SECUNDARIA: DADOS DEL SISTEMA *
*****************************************/

// SQL para obtener la lista de dados asociados al sistema (JOIN tabla puente)
$sql2 = "SELECT d.Dado 
         FROM dado d
         INNER JOIN dado_sistema ds ON d.ID_Dado = ds.ID_Dado
         WHERE ds.ID_Sistema = ?";

$stmt2 = $conn->prepare($sql2); // Preparar la consulta de dados

// Validar que la preparación sea correcta
if(!$stmt2){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta de dado']);
    exit;
}

$stmt2->bind_param('i', $id); // Enlazar ID y ejecutar
$stmt2->execute();
$res2 = $stmt2->get_result(); // Obtener resultados

$dado = []; // Inicializar arreglo para acumular los dados (puede haber múltiples)

// Recorrer todas las filas y añadir el valor de 'Dado' al arreglo
while($r = $res2->fetch_assoc()) {
    $dado[] = $r['Dado'];
}

$stmt2->close(); // Cerrar el statement de dados

/******************************************
* CONSULTA TERCIARIA: GÉNEROS DEL SISTEMA *
******************************************/

// SQL para obtener la lista de géneros asociados al sistema (JOIN tabla puente)
$sql3 = 'SELECT g.Genero 
         FROM genero g
         INNER JOIN genero_sistema gs ON g.ID_Genero = gs.ID_Genero
         WHERE gs.ID_Sistema = ?';

$stmt3 = $conn->prepare($sql3); // Preparar la consulta de géneros

// Validar preparación
if(!$stmt3){
    echo json_encode(['success'=>false, 'message'=>'Error en la consulta de genero']);
    exit;
}

$stmt3->bind_param('i', $id); // Enlazar ID y ejecutar
$stmt3->execute();
$res3 = $stmt3->get_result(); // Obtener resultados

$genero = []; // Inicializar arreglo para géneros (puede haber múltiples)

// Recorrer filas y añadir cada 'Genero' al arreglo
while($r = $res3->fetch_assoc()) {
    $genero[] = $r['Genero'];
}

$stmt3->close(); // Cerrar el statement de géneros

/*****************
* RESPUESTA JSON *
*****************/

// Enviar la respuesta en formato JSON con todos los datos recopilados
// Estructura compatible con form_partida.js
echo json_encode([
    'success' => true,
    'descripcion' => $row['Descripcion'],
    'clasificacion' => $row['Clasificacion'],
    'tipo' => $row['Tipo'],
    'genero' => $genero,
    'dado' => $dado
]);