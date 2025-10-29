<?php
// API para verificar si un correo ya está registrado

include '../conexion.php'; // Incluir la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8'); // Respuesta en formato JSON

/***************************
* VALIDACIÓN DE PARÁMETROS *
***************************/

// Verificar que exista el parámetro 'email'
if (!isset($_GET['email']) || empty(trim($_GET['email']))) {
    echo json_encode([
        'success' => false,
        'message' => 'Email no proporcionado'
    ]);
    exit;
}

$email = trim($_GET['email']);

// Validar formato del correo
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Formato de email inválido',
        'existe' => false
    ]);
    exit;
}

/*************************************
* CONSULTA: VERIFICAR SI EMAIL EXISTE *
*************************************/

$sql = "SELECT ID_Usuarios FROM usuarios WHERE Correo = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta'
    ]);
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

$existe = ($result->num_rows > 0);

$stmt->close();

/*****************
* RESPUESTA JSON *
*****************/

echo json_encode([
    'success' => true,
    'existe' => $existe,
    'message' => $existe ? 'El correo ya está registrado' : 'Correo disponible'
]);