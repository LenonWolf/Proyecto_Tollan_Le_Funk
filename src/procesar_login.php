<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_name('TOLLAN_SESSION');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        @ini_set('session.cookie_secure', '1');
    }
    session_start();
}

require_once 'config.php';
require_once 'conexion.php';

$db = new Conexion('usr_lec_usuarios', 'lec_usuarios123');
$conn = $db->conectar();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido']);
    exit;
}

$sql = "SELECT ID_Usuarios, Nombre, Correo, Contraseña, Fecha_Alt, Tipo_Usr FROM usuarios WHERE Correo = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    $stmt->close();
    $db->cerrar();
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $row['Contraseña'])) {
    echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    $db->cerrar();
    exit;
}

$_SESSION['ID_Usuarios'] = $row['ID_Usuarios'];
$_SESSION['Nombre'] = $row['Nombre'];
$_SESSION['Correo'] = $row['Correo'];
$_SESSION['Fecha_Alt'] = $row['Fecha_Alt'];
$_SESSION['Tipo_Usr'] = $row['Tipo_Usr'];

session_regenerate_id(true);

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : null;

if ($redirect) {
    $redirect = str_replace('/Tollan_Le_Funk/', '', $redirect);
    $redirect = ltrim($redirect, '/');
    $redirect = url($redirect);
}

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
}

echo json_encode([
    'success' => true,
    'message' => '¡Bienvenido ' . $row['Nombre'] . '!',
    'redirect' => $redirect,
    'tipo_usuario' => $row['Tipo_Usr']
]);

$db->cerrar();