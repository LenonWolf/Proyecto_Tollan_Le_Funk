<?php
// Procesamiento de registro con respuesta JSON para AJAX

include 'conexion.php'; // Conexión a la base de datos

header('Content-Type: application/json; charset=utf-8'); // Respuesta JSON

date_default_timezone_set('America/Mexico_City');

/***************************
* VERIFICAR MÉTODO POST *
***************************/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

/***************************
* RECIBIR Y VALIDAR DATOS *
***************************/

$nombre = isset($_POST['username']) ? trim($_POST['username']) : '';
$correo = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validar campos vacíos
if (empty($nombre) || empty($correo) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar formato del correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo electrónico no es válido'
    ]);
    exit;
}

// Validar longitud de la contraseña (mínimo 6 caracteres)
if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 6 caracteres'
    ]);
    exit;
}

/*********************************
* VERIFICAR SI EL CORREO YA EXISTE *
*********************************/

$sql_check = "SELECT ID_Usuarios FROM usuarios WHERE Correo = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
    exit;
}

$stmt_check->bind_param("s", $correo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Este correo ya está registrado'
    ]);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

/**********************
* INSERTAR NUEVO USUARIO *
**********************/

// Encriptar la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql_insert = "INSERT INTO usuarios (Nombre, Correo, Contraseña, Fecha_Alt, Tipo_Usr)
               VALUES (?, ?, ?, ?, 'Usr')";
$stmt_insert = $conn->prepare($sql_insert);

if (!$stmt_insert) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar el registro'
    ]);
    exit;
}

$fecha_alt = date("Y-m-d");
$stmt_insert->bind_param("ssss", $nombre, $correo, $password_hash, $fecha_alt);

if ($stmt_insert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => '¡Registro exitoso! Redirigiendo al login...',
        'usuario' => $nombre
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar usuario: ' . $stmt_insert->error
    ]);
}

$stmt_insert->close();
$conn->close();