<?php

/********************************
* CONFIGURACIÓN DE CREDENCIALES *
********************************/

$host = "localhost"; // Nombre del servidor de la base de datos (localhost si la BD está en el mismo servidor que la app)
$user = "root"; // Usuario con permisos para acceder a la base de datos
$password = "#1W2O3L4F5m"; // Contraseña del usuario anterior
$database = "tollan"; // Nombre de la base de datos a la que se conectará la aplicación

/***********************************
* CREACIÓN DE LA CONEXIÓN (MySQLi) *
***********************************/

// Crear un nuevo objeto de conexión MySQLi usando los parámetros anteriores.
// Esto intenta establecer una conexión TCP al servidor MySQL.
$conn = new mysqli($host, $user, $password, $database);

/**************************************
* VERIFICACIÓN DE ERRORES DE CONEXIÓN *
**************************************/

// La propiedad connect_error contiene el mensaje de error si la conexión falló.
// Si hay error, se detiene la ejecución del script con un mensaje explicativo.
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

/********************************
* CONFIGURACIÓN DE CODIFICACIÓN *
********************************/

// Establecer el conjunto de caracteres a UTF-8 para:
// - Interpretar correctamente acentos y caracteres especiales.
// - Evitar problemas de codificación al enviar/recibir datos.
$conn->set_charset("utf8");
?>