<?php
// Borrador de partidas

include 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Obtener el ID enviado por POST. 
// Si no existe, se asigna null gracias al operador null coalescing (??).
$id = $_POST['id'] ?? null;

// Verificar si se recibió un ID válido
if ($id) {
    $stmt = $conn->prepare("DELETE FROM partida WHERE ID_Partida = ?"); // Preparar consulta SQL para eliminar la partida con el ID recibido
    $stmt->bind_param("i", $id); // Asociar el parámetro ID a la consulta (i = integer)
    $stmt->execute(); // Ejecutar la consulta preparada
    $stmt->close(); // Cerrar el statement para liberar recursos
    
    // Responder con código HTTP 200 (OK) indicando éxito
    http_response_code(200); 
} else {
    // Si no se recibió un ID válido, responder con código HTTP 400 (Bad Request)
    http_response_code(400); 
}
?>