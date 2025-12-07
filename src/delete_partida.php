<?php
session_name('TOLLAN_SESSION');
session_start();
header('Content-Type: application/json');

// DEBUG: Log del método recibido
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION: " . print_r($_SESSION, true));

if(!isset($_SESSION['ID_Usuarios'])){
    http_response_code(401);
    die(json_encode(['error'=>'No autenticado']));
}

if(!in_array($_SESSION['Tipo_Usr'],['Adm','Mod'])){
    http_response_code(403);
    die(json_encode(['error'=>'Sin permisos']));
}

// Cambiado: Aceptar tanto POST como OPTIONS (para CORS)
if($_SERVER['REQUEST_METHOD']!=='POST' && $_SERVER['REQUEST_METHOD']!=='OPTIONS'){
    http_response_code(405);
    die(json_encode([
        'error'=>'Método no permitido',
        'method_received'=>$_SERVER['REQUEST_METHOD'],
        'expected'=>'POST'
    ]));
}

// Si es OPTIONS (preflight), responder OK
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){
    http_response_code(200);
    die(json_encode(['status'=>'OK']));
}

$id=$_POST['id']??null;
if(!$id||!is_numeric($id)){
    http_response_code(400);
    die(json_encode(['error'=>'ID inválido','received_id'=>$id]));
}

require_once 'conexion.php';
$db=new Conexion('usr_del_partida','del_partida123');
$conn=$db->conectar();
$stmt=$conn->prepare("DELETE FROM partida WHERE ID_Partida = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$affected=$stmt->affected_rows;
$stmt->close();
$db->cerrar();

echo json_encode(['success'=>true,'affected'=>$affected]);