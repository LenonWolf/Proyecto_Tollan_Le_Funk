<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

try {
    // Conectar con el usuario de lectura
    $db = new Conexion('usr_lec_usuarios', 'lec_usuarios123');
    $conn = $db->conectar();

    // Ejecutar SELECT sobre la tabla usuarios
    $sql = "SELECT ID_Usuarios, Nombre, Correo, Contraseña, Fecha_Alt, Tipo_Usr 
            FROM usuarios 
            LIMIT 5"; // límite para no traer demasiados registros

    $result = $conn->query($sql);

    if ($result) {
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        echo json_encode([
            "success" => true,
            "message" => "Consulta ejecutada correctamente",
            "usuarios" => $usuarios
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error en la consulta: " . $conn->error
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