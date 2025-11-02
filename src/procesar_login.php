<?php
// Procesamiento de login con validación de credenciales

session_start(); // Iniciar sesión

include 'conexion.php'; // Incluir la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8'); // Respuesta JSON

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

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validar campos vacíos
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa todos los campos'
    ]);
    exit;
}

// Validar formato del correo
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo electrónico no es válido'
    ]);
    exit;
}

/*********************************
* BUSCAR USUARIO EN LA BASE DE DATOS *
*********************************/

$sql = "SELECT ID_Usuarios, Nombre, Correo, Contraseña, Fecha_Alt, Tipo_Usr 
        FROM usuarios 
        WHERE Correo = ? 
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor'
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Correo o contraseña incorrectos'
    ]);
    $stmt->close();
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

/********************************
* VERIFICAR CONTRASEÑA *
********************************/

// Comparar la contraseña ingresada con el hash almacenado
if (!password_verify($password, $row['Contraseña'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Correo o contraseña incorrectos'
    ]);
    exit;
}

/********************************
* INICIAR SESIÓN DEL USUARIO *
********************************/

// Guardar datos del usuario en la sesión
$_SESSION['ID_Usuarios'] = $row['ID_Usuarios'];
$_SESSION['Nombre'] = $row['Nombre'];
$_SESSION['Correo'] = $row['Correo'];
$_SESSION['Fecha_Alt'] = $row['Fecha_Alt'];
$_SESSION['Tipo_Usr'] = $row['Tipo_Usr'];

// Determinar la página de redirección según si venía de alguna página específica
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : null;

// Si no hay redirect o no tiene permisos, redirigir según el rol
if (!$redirect) {
    switch ($row['Tipo_Usr']) {
        case 'Adm':
        case 'Mod':
            $redirect = '/Tollan_Le_Funk/index.php';
            break;
        case 'Usr':
            $redirect = '/Tollan_Le_Funk/ver_partida.php';
            break;
        default:
            $redirect = '/Tollan_Le_Funk/index.php';
    }
} else {
    // Verificar si tiene permisos para la página solicitada
    $redirect = validarAcceso($redirect, $row['Tipo_Usr']);
}

/*****************
* RESPUESTA JSON *
*****************/

echo json_encode([
    'success' => true,
    'message' => '¡Bienvenido ' . $row['Nombre'] . '!',
    'redirect' => $redirect,
    'tipo_usuario' => $row['Tipo_Usr']
]);

$conn->close();

/********************************
* FUNCIÓN DE VALIDACIÓN DE ACCESO *
********************************/

function validarAcceso($pagina, $tipo_usr) {
    // Páginas que requieren permisos de administrador o moderador
    $paginasRestringidas = [
        '/Tollan_Le_Funk/crear_partida.php',
        '/Tollan_Le_Funk/editar_partida.php'
    ];
    
    // Si es usuario normal y trata de acceder a páginas restringidas
    if ($tipo_usr === 'Usr' && in_array($pagina, $paginasRestringidas)) {
        return '/Tollan_Le_Funk/ver_partida.php'; // Redirigir a ver partidas
    }
    
    return $pagina; // Permitir acceso
}