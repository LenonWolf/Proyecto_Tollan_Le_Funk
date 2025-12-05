<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando login...<br>";

// Probar cargar config
if (file_exists('config.php')) {
    echo "Config existe<br>";
    require_once 'config.php';
} else {
    die("Config NO existe");
}

echo "Config cargado<br>";

// Iniciar sesión simple
session_start();
echo "Sesión iniciada<br>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Test</title>
</head>
<body>
    <h1>Login de Usuario</h1>
    <p>Si ves esto, PHP está funcionando.</p>
    
    <form id="form-login" action="procesar_login.php" method="POST">
        <div>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
        
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button id="btn-login" type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>