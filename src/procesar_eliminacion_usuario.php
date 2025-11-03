<?php
// Procesamiento de eliminación de cuenta de usuario

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

$confirmar_delete = isset($_POST['confirmar_delete']) ? trim($_POST['confirmar_delete']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$id_usuario = $_SESSION['ID_Usuarios'];

// Validar campos vacíos
if (empty($confirmar_delete) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar palabra de confirmación
if (strtoupper($confirmar_delete) !== 'ELIMINAR') {
    echo json_encode([
        'success' => false,
        'message' => 'Debes escribir exactamente "ELIMINAR" para confirmar'
    ]);
    exit;
}

/*********************************
* VERIFICAR CONTRASEÑA *
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

// Verificar que la contraseña sea correcta
if (!password_verify($password, $row['Contraseña'])) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña es incorrecta'
    ]);
    exit;
}

/**********************
* ELIMINAR USUARIO *
**********************/

// Nota: Si tienes tablas relacionadas (partidas creadas, etc.), 
// deberías manejar esas relaciones aquí (CASCADE o eliminación manual)

$sql_delete = "DELETE FROM usuarios WHERE ID_Usuarios = ?";
$stmt_delete = $conn->prepare($sql_delete);

if (!$stmt_delete) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar la eliminación'
    ]);
    exit;
}

$stmt_delete->bind_param("i", $id_usuario);

if ($stmt_delete->execute()) {
    // Destruir la sesión
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cuenta eliminada correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la cuenta: ' . $stmt_delete->error
    ]);
}

$stmt_delete->close();
$conn->close();