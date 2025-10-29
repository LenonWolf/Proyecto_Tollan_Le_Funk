<?php
// --- CONFIGURACIÓN BÁSICA ---
include 'conexion.php'; // Conexión a la base de datos
date_default_timezone_set('America/Mexico_City');

// --- VERIFICAR QUE VIENE POR POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recibir y sanitizar los datos del formulario
    $nombre = trim($_POST['username']);
    $correo = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar campos vacíos
    if (empty($nombre) || empty($correo) || empty($password) || empty($confirm_password)) {
        die("Por favor, completa todos los campos.");
    }

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden. <a href='registro.php'>Volver</a>");
    }

    // Validar formato del correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("El correo electrónico no es válido.");
    }

    // Verificar si el correo ya existe
    $sql_check = "SELECT ID_Usuarios FROM usuarios WHERE Correo = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $correo);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("Este correo ya está registrado. <a href='registro.php'>Volver</a>");
    }
    $stmt_check->close();

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql_insert = "INSERT INTO usuarios (Nombre, Correo, Contraseña, Fecha_Alt, Tipo_Usr)
                   VALUES (?, ?, ?, ?, 'Usr')";
    $stmt_insert = $conn->prepare($sql_insert);

    $fecha_alt = date("Y-m-d");
    $stmt_insert->bind_param("ssss", $nombre, $correo, $password_hash, $fecha_alt);

    if ($stmt_insert->execute()) {
        echo "<h2>Registro exitoso 🎉</h2>";
        echo "<p>Ya puedes iniciar sesión con tu cuenta.</p>";
        echo "<a href='login.php'>Ir al inicio de sesión</a>";
    } else {
        echo "Error al registrar usuario: " . $stmt_insert->error;
    }

    $stmt_insert->close();
    $conn->close();

} else {
    // Si se intenta acceder directamente sin enviar el formulario
    header("Location: registro.php");
    exit();
}
?>
