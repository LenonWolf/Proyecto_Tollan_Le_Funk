<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

try {
    $db = new Conexion('usr_lec_usuarios', 'lec_usuarios123');
    $conn = $db->conectar();

    // Ejecutar una consulta mínima
    $result = $conn->query("SELECT NOW() AS fecha_actual");

    if ($result) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "message" => "Conexión exitosa",
            "fecha_actual" => $row['fecha_actual']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Consulta fallida"
        ]);
    }

    $db->cerrar();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión: " . $e->getMessage()
    ]);
}
?>