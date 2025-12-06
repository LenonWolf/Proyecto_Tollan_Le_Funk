<?php
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']);

require_once 'config.php';
require_once 'conexion.php';

$db = new Conexion('usr_edit_partida', 'edit_partida123');
$conn = $db->conectar();

function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

$ID_Usuarios = $_SESSION['ID_Usuarios'];
$ID_Sistema = intval(post('ID_Sistema'));
$ID_DM = post('ID_DM');
$dm_nombre = post('lbl-nombre');
$dm_fecha_nac = post('lbl-fecha-nac');
$Titulo = post('Titulo');
$Fecha_Inic = post('Fecha_Inic');
$Horario = post('Horario');
$Periocidad = post('Periocidad');
$No_Jugadores = intval(post('No_Jugadores'));

date_default_timezone_set('America/Mexico_City');
$hoy = date('Y-m-d');

$errors = [];
if (!$ID_Sistema) $errors[] = 'Seleccione un sistema.';
if (!$Titulo) $errors[] = 'Ingrese título.';
if (!$Fecha_Inic) $errors[] = 'Ingrese fecha de inicio.';
if (strtotime($Fecha_Inic) < strtotime($hoy)) $errors[] = 'La fecha de inicio no puede ser anterior a hoy.';
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

if (!empty($errors)) {
    $msg = urlencode(implode(' | ', $errors));
    $db->cerrar();
    header("Location: " . url('crear_partida.php') . "?error=$msg");
    exit;
}

if ($ID_DM === 'new' || $ID_DM === '' ) {
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        $db->cerrar();
        header("Location: " . url('crear_partida.php') . "?error=" . urlencode("Datos del DM incompletos."));
        exit;
    }

    $sql_check = "SELECT ID_DM FROM dm WHERE Nombre = ? AND Fecha_Nac = ? LIMIT 1";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param('ss', $dm_nombre, $dm_fecha_nac);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $dm_id_to_use = $row['ID_DM'];
    } else {
        $sql_insert_dm = "INSERT INTO dm (Nombre, Fecha_Nac, Fecha_Alt) VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql_insert_dm);
        $stmt2->bind_param('ss', $dm_nombre, $dm_fecha_nac);

        if (!$stmt2->execute()) {
            $db->cerrar();
            header("Location: " . url('crear_partida.php') . "?error=" . urlencode("Error al crear DM."));
            exit;
        }

        $dm_id_to_use = $stmt2->insert_id;
    }
} else {
    $dm_id_to_use = intval($ID_DM);
    if ($dm_id_to_use <= 0) {
        $db->cerrar();
        header("Location: " . url('crear_partida.php') . "?error=" . urlencode("DM inválido."));
        exit;
    }
}

$sql_insert_partida = "INSERT INTO partida (ID_Usuarios, ID_Sistema, ID_DM, Titulo, Fecha_Inic, Horario, Periocidad, No_Jugadores)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmtp = $conn->prepare($sql_insert_partida);

$stmtp->bind_param('iiissssi',
    $ID_Usuarios,
    $ID_Sistema,
    $dm_id_to_use,
    $Titulo,
    $Fecha_Inic,
    $Horario,
    $Periocidad,
    $No_Jugadores
);

if (!$stmtp->execute()) {
    $db->cerrar();
    header("Location: " . url('crear_partida.php') . "?error=" . urlencode("Error al crear partida: " . $conn->error));
    exit;
}

$db->cerrar();
header("Location: " . url('ver_partida.php') . "?created=1");
exit;