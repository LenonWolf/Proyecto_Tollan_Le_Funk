<?php
// Procesamiento de cambio de contraseña

session_start();

include 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

/***************************
* VERIFICAR AUTENTICACIÓN *
***************************/

if (!isset($_SESSION['ID_Usuarios'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

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

$password_actual = isset($_POST['password_actual']) ? $_POST['password_actual'] : '';
$nueva_password = isset($_POST['nueva_password']) ? $_POST['nueva_password'] : '';
$id_usuario = $_SESSION['ID_Usuarios'];

// Validar campos vacíos
if (empty($password_actual) || empty($nueva_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar longitud de la nueva contraseña
if (strlen($nueva_password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'La nueva contraseña debe tener al menos 6 caracteres'
    ]);
    exit;
}

/*********************************
* VERIFICAR CONTRASEÑA ACTUAL *
*********************************/

$sql_check = "SELECT Contraseña FROM usuarios WHERE ID_Usuarios = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
    exit;
}

$stmt_check->bind_param("i", $id_usuario);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado'
    ]);
    $stmt_check->close();
    exit;
}

$row = $result->fetch_assoc();
$stmt_check->close();

// Verificar que la contraseña actual sea correcta
if (!password_verify($password_actual, $row['Contraseña'])) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña actual es incorrecta'
    ]);
    exit;
}

// Verificar que la nueva contraseña sea diferente
if ($password_actual === $nueva_password) {
    echo json_encode([
        'success' => false,
        'message' => 'La nueva contraseña debe ser diferente a la actual'
    ]);
    exit;
}

/**********************
* ACTUALIZAR CONTRASEÑA *
**********************/

$password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

$sql_update = "UPDATE usuarios SET Contraseña = ? WHERE ID_Usuarios = ?";
$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar la actualización'
    ]);
    exit;
}

$stmt_update->bind_param("si", $password_hash, $id_usuario);

if ($stmt_update->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Contraseña cambiada correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar la contraseña: ' . $stmt_update->error
    ]);
}

$stmt_update->close();
$conn->close();