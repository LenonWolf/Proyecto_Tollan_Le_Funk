<?php
// Archivo de prueba simple
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['test' => 'OK', 'message' => 'El archivo funciona']);
?>