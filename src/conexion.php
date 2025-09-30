<?php
$host = "localhost";
$user = "root";
$password = "#1W2O3L4F5m";
$database = "tollan";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>