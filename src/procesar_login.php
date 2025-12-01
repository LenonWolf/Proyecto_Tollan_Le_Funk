<?php
header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "message" => "Prueba OK",
    "redirect" => "index.php"
]);
?>