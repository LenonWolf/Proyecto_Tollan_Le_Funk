<?php 
// submit_partida.php
include 'conexion.php';

function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

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
if (strtotime($Fecha_Inic) < strtotime($hoy)) $errors[] = 'La fecha de inicio no puede ser anterior a hoy.';
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

if (!empty($errors)) {
    $msg = urlencode(implode(' | ', $errors));
    header("Location: ../crear_partida.php?error=$msg");
    exit;
}

// --------------------
// Procesar DM
// --------------------
if ($ID_DM === 'new' || $ID_DM === '' ) {
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        header("Location: ../crear_partida.php?error=" . urlencode("Datos del DM incompletos."));
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
        // Insertar nuevo DM con Fecha_Alt = NOW()
        $sql_insert_dm = "INSERT INTO dm (Nombre, Fecha_Nac, Fecha_Alt) VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql_insert_dm);
        $stmt2->bind_param('ss', $dm_nombre, $dm_fecha_nac);
        if (!$stmt2->execute()) {
            header("Location: ../crear_partida.php?error=" . urlencode("Error al crear DM."));
            exit;
        }
        $dm_id_to_use = $stmt2->insert_id;
    }
} else {
    // Usaron un DM existente
    $dm_id_to_use = intval($ID_DM);
    if ($dm_id_to_use <= 0) {
        header("Location: ../crear_partida.php?error=" . urlencode("DM inválido."));
        exit;
    }
}

// --------------------
// Insertar partida
// --------------------
$sql_insert_partida = "INSERT INTO partida (ID_Sistema, ID_DM, Titulo, Fecha_Inic, Horario, Periocidad, No_Jugadores)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmtp = $conn->prepare($sql_insert_partida);
$stmtp->bind_param('iissssi',
    $ID_Sistema,
    $dm_id_to_use,
    $Titulo,
    $Fecha_Inic,
    $Horario,
    $Periocidad,
    $No_Jugadores
);

if (!$stmtp->execute()) {
    header("Location: ../crear_partida.php?error=" . urlencode("Error al crear partida: " . $conn->error));
    exit;
}

// Éxito
header("Location: ../ver_partida.php?created=1");
exit;