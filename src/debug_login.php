<?php
// Activar errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Debug de Login</h1>";
echo "<p>Probando archivos necesarios...</p>";

// Probar session_config.php
echo "<h2>1. Probando session_config.php</h2>";
if (file_exists('session_config.php')) {
    echo "✅ Archivo existe<br>";
    try {
        require_once 'session_config.php';
        echo "✅ Se cargó correctamente<br>";
    } catch (Exception $e) {
        echo "❌ Error al cargar: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Archivo NO existe en: " . __DIR__ . "/session_config.php<br>";
}

// Probar config.php
echo "<h2>2. Probando config.php</h2>";
if (file_exists('config.php')) {
    echo "✅ Archivo existe<br>";
    try {
        require_once 'config.php';
        echo "✅ Se cargó correctamente<br>";
        echo "BASE_PATH definido: " . (defined('BASE_PATH') ? BASE_PATH : 'NO') . "<br>";
    } catch (Exception $e) {
        echo "❌ Error al cargar: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Archivo NO existe en: " . __DIR__ . "/config.php<br>";
}

// Probar session_start
echo "<h2>3. Probando session_start()</h2>";
try {
    session_start();
    echo "✅ Sesión iniciada correctamente<br>";
    echo "Session ID: " . session_id() . "<br>";
} catch (Exception $e) {
    echo "❌ Error al iniciar sesión: " . $e->getMessage() . "<br>";
}

// Listar archivos en el directorio
echo "<h2>4. Archivos en src/</h2>";
echo "<pre>";
print_r(scandir(__DIR__));
echo "</pre>";

echo "<h2>5. Información del servidor</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";

echo "<hr>";
echo "<p><a href='login.php'>Intentar cargar login.php</a></p>";
?>