<?php  
// update_partida.php
include 'conexion.php';

function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

$ID_Sistema = intval(post('ID_Sistema'));
$ID_DM = post('ID_DM');

// Datos posible nuevo DM
$dm_nombre = post('lbl-nombre');
$dm_fecha_nac = post('lbl-fecha-nac');

// Datos partida
$Titulo = post('Titulo');
$Fecha_Inic = post('Fecha_Inic');
$Horario = post('Horario');
$Periocidad = post('Periocidad');
$No_Jugadores = intval(post('No_Jugadores'));

date_default_timezone_set('America/Mexico_City');
$hoy = date('Y-m-d');

// --------------------
// Validaciones básicas
// --------------------
$errors = [];
if (!$ID_Sistema) $errors[] = 'Seleccione un sistema.';
if (!$Titulo) $errors[] = 'Ingrese título.';
if (!$Fecha_Inic) $errors[] = 'Ingrese fecha de inicio.';
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

if (!empty($errors)) {
    $msg = implode(' | ', $errors);
    echo "<script>alert('Error: $msg'); window.history.back();</script>";
    exit;
}

// --------------------
// Procesar DM
// --------------------
if ($ID_DM === 'new' || $ID_DM === '' ) {
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        echo "<script>alert('Datos del DM incompletos.'); window.history.back();</script>";
        exit;
    }
    // Buscar si ya existe
    $sql_check = "SELECT ID_DM FROM dm WHERE Nombre = ? AND Fecha_Nac = ? LIMIT 1";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param('ss', $dm_nombre, $dm_fecha_nac);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $dm_id_to_use = $row['ID_DM'];
    } else {
        // Insertar nuevo DM
        $sql_insert_dm = "INSERT INTO dm (Nombre, Fecha_Nac, Fecha_Alt) VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql_insert_dm);
        $stmt2->bind_param('ss', $dm_nombre, $dm_fecha_nac);
        if (!$stmt2->execute()) {
            echo "<script>alert('Error al crear DM.'); window.history.back();</script>";
            exit;
        }
        $dm_id_to_use = $stmt2->insert_id;
    }
} else {
    // Usaron un DM existente
    $dm_id_to_use = intval($ID_DM);
    if ($dm_id_to_use <= 0) {
        echo "<script>alert('DM inválido.'); window.history.back();</script>";
        exit;
    }
}

// --------------------
// Actualizar partida
// --------------------
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
    echo "<script>alert('Error al actualizar partida: " . addslashes($conn->error) . "'); window.history.back();</script>";
    exit;
}

// --------------------
// Éxito → refrescar padre y cerrar popup
// --------------------
echo "<script>
    if (window.opener && !window.opener.closed) {
        window.opener.location.reload(); // refresca editar_partida.php
    }
    window.close(); // cierra modificar_partida.php
</script>";
exit;