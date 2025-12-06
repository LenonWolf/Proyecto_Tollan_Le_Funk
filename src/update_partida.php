<?php
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']);

require_once 'config.php';
require_once 'conexion.php';

$db = new Conexion('usr_edit_partida', 'edit_partida123');
$conn = $db->conectar();

function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

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
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

if (!empty($errors)) {
    $msg = implode(' | ', $errors);
    $db->cerrar();
    echo "<script>alert('Error: $msg'); window.history.back();</script>";
    exit;
}

if ($ID_DM === 'new' || $ID_DM === '' ) {
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        $db->cerrar();
        echo "<script>alert('Datos del DM incompletos.'); window.history.back();</script>";
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
            echo "<script>alert('Error al crear DM.'); window.history.back();</script>";
            exit;
        }

        $dm_id_to_use = $stmt2->insert_id;
    }
} else {
    $dm_id_to_use = intval($ID_DM);
    if ($dm_id_to_use <= 0) {
        $db->cerrar();
        echo "<script>alert('DM inválido.'); window.history.back();</script>";
        exit;
    }
}

$sql_update_partida = "UPDATE partida SET ID_Sistema = ?, ID_DM = ?, Titulo = ?, Fecha_Inic = ?, Horario = ?, Periocidad = ?, No_Jugadores = ?
                       WHERE ID_Partida = ?";
$stmtp = $conn->prepare($sql_update_partida);

$stmtp->bind_param('iissssii',
    $ID_Sistema,
    $dm_id_to_use,
    $Titulo,
    $Fecha_Inic,
    $Horario,
    $Periocidad,
    $No_Jugadores,
    $id
);

if (!$stmtp->execute()) {
    $db->cerrar();
    echo "<script>alert('Error al actualizar partida: " . addslashes($conn->error) . "'); window.history.back();</script>";
    exit;
}

$db->cerrar();
echo "<script>
    if (window.opener && !window.opener.closed) {
        window.opener.location.reload();
    }
    window.close();
</script>";
exit;