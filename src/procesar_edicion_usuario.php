<?php
// Procesamiento de edición de datos del usuario

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

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$id_usuario = $_SESSION['ID_Usuarios'];

// Validar campos vacíos
if (empty($nombre) || empty($correo)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar longitud del nombre
if (strlen($nombre) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre debe tener al menos 3 caracteres'
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

/*********************************
* VERIFICAR SI EL CORREO YA EXISTE *
*********************************/

// Solo verificar si el correo cambió
if ($correo !== $_SESSION['Correo']) {
    $sql_check = "SELECT ID_Usuarios FROM usuarios WHERE Correo = ? AND ID_Usuarios != ? LIMIT 1";
    $stmt_check = $conn->prepare($sql_check);
    
    if (!$stmt_check) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor'
        ]);
        exit;
    }
    
    $stmt_check->bind_param("si", $correo, $id_usuario);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Este correo ya está registrado por otro usuario'
        ]);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();
}

/**********************
* ACTUALIZAR USUARIO *
**********************/

$sql_update = "UPDATE usuarios SET Nombre = ?, Correo = ? WHERE ID_Usuarios = ?";
$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar la actualización'
    ]);
    exit;
}

$stmt_update->bind_param("ssi", $nombre, $correo, $id_usuario);

if ($stmt_update->execute()) {
    // Actualizar las variables de sesión
    $_SESSION['Nombre'] = $nombre;
    $_SESSION['Correo'] = $correo;
    
    echo json_encode([
        'success' => true,
        'message' => 'Datos actualizados correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar los datos: ' . $stmt_update->error
    ]);
}

$stmt_update->close();
$conn->close();