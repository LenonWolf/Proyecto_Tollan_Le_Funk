<?php

// Procesamiento de login con validación de credenciales

// Configurar sesión usando archivo centralizado
require_once 'session_config.php';

session_start(); // Iniciar sesión

require_once 'config.php'; // Cargar configuración de rutas
require_once 'conexion.php';

$db = new Conexion('usr_lec_usuarios', 'lec_usuarios123');
$conn = $db->conectar();

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
    $db->cerrar();
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
    $db->cerrar();
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

// IMPORTANTE: Regenerar ID de sesión por seguridad
session_regenerate_id(true);

// Log para debug (remover en producción)
error_log("Login exitoso - Usuario: " . $row['Nombre'] . " - Session ID: " . session_id());

// Determinar la página de redirección según si venía de alguna página específica
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : null;

// Limpiar el redirect si viene con /Tollan_Le_Funk/
if ($redirect) {
    $redirect = limpiarRuta($redirect);
}

// Si no hay redirect o no tiene permisos, redirigir según el rol
if (!$redirect) {
    switch ($row['Tipo_Usr']) {
        case 'Adm':
        case 'Mod':
            $redirect = url('index.php');
            break;
        case 'Usr':
            $redirect = url('ver_partida.php');
            break;
        default:
            $redirect = url('index.php');
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

$db->cerrar();

/********************************
* FUNCIÓN PARA LIMPIAR RUTAS *
********************************/

/**
 * Limpia las rutas que vienen con /Tollan_Le_Funk/ del entorno local
 * y las convierte a rutas compatibles con el entorno actual
 */
function limpiarRuta($ruta) {
    // Remover /Tollan_Le_Funk/ si existe
    $ruta = str_replace('/Tollan_Le_Funk/', '', $ruta);
    
    // Remover slash inicial si existe
    $ruta = ltrim($ruta, '/');
    
    // Aplicar la función url() para generar la ruta correcta
    return url($ruta);
}

/********************************
* FUNCIÓN DE VALIDACIÓN DE ACCESO *
********************************/

function validarAcceso($pagina, $tipo_usr) {
    // Páginas que requieren permisos de administrador o moderador
    $paginasRestringidas = [
        url('crear_partida.php'),
        url('editar_partida.php')
    ];
    
    // Si es usuario normal y trata de acceder a páginas restringidas
    if ($tipo_usr === 'Usr' && in_array($pagina, $paginasRestringidas)) {
        return url('ver_partida.php'); // Redirigir a ver partidas
    }
    
    return $pagina; // Permitir acceso
}