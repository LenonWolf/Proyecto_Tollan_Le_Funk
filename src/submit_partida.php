<?php
// Responsabilidad: recibir datos por POST, validar entradas, (opcionalmente crear un nuevo DM), e insertar la partida en la BD.

session_start(); // Iniciar sesión para obtener el usuario logueado

// Verificar autenticación
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']); // Solo Admin y Moderadores pueden crear partidas

require_once 'conexion.php';

// Crear conexión con usuario de solo lectura
$db = new Conexion('usr_edit_partida', 'edit_partida123');
$conn = $db->conectar();

// Función auxiliar para obtener un valor de $_POST.
// - Si existe, lo recorta (trim) para quitar espacios extra al inicio/fin.
// - Si no existe, devuelve null.
// Esto estandariza la lectura y evita repetición de isset(...)?...:...
function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

/******************************************
* EXTRACCIÓN DE PARÁMETROS DEL FORMULARIO *
******************************************/

// Obtener el ID del usuario de la sesión
$ID_Usuarios = $_SESSION['ID_Usuarios'];

$ID_Sistema = intval(post('ID_Sistema')); // Sistema seleccionado (entero)
$ID_DM = post('ID_DM'); // Puede ser ID existente o 'new'

// Datos de posible nuevo DM (se usan si $ID_DM === 'new' o vacío)
$dm_nombre = post('lbl-nombre'); // Nombre del DM nuevo
$dm_fecha_nac = post('lbl-fecha-nac'); // Fecha de nacimiento del DM nuevo (YYYY-MM-DD)

// Datos de la partida
$Titulo = post('Titulo');
$Fecha_Inic = post('Fecha_Inic'); // Fecha de inicio (YYYY-MM-DD)
$Horario = post('Horario'); // Hora (HH:MM)
$Periocidad = post('Periocidad'); // Frecuencia (Semanal, Quincenal, One_Shot)
$No_Jugadores = intval(post('No_Jugadores')); // Número de jugadores (entero >= 1)

// Definir zona horaria y obtener la fecha actual (para restricciones en el formulario)
date_default_timezone_set('America/Mexico_City');
$hoy = date('Y-m-d');

/**************************************
* VALIDACIONES BÁSICAS DEL FORMULARIO *
**************************************/

// Recolectar errores para devolverlos
$errors = [];
if (!$ID_Sistema) $errors[] = 'Seleccione un sistema.';
if (!$Titulo) $errors[] = 'Ingrese título.';
if (!$Fecha_Inic) $errors[] = 'Ingrese fecha de inicio.';
if (strtotime($Fecha_Inic) < strtotime($hoy)) $errors[] = 'La fecha de inicio no puede ser anterior a hoy.';
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

// Si hay errores, redirigir a la pantalla de creación con los mensajes codificados en la URL
if (!empty($errors)) {
    $msg = urlencode(implode(' | ', $errors)); // Unir errores con separador visual
    header("Location: ../crear_partida.php?error=$msg");
    exit; // Detener ejecución para evitar acciones posteriores
}

/*******************************************
* PROCESAMIENTO DEL DM (EXISTENTE O NUEVO) *
*******************************************/

// Si el usuario eligió "nuevo DM" o dejó vacío el campo de DM:
if ($ID_DM === 'new' || $ID_DM === '' ) {
    // Validar que se hayan proporcionado los datos indispensables del nuevo DM
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        $db->cerrar();
        header("Location: ../crear_partida.php?error=" . urlencode("Datos del DM incompletos."));
        exit;
    }

    // Comprobar si ya existe un DM con mismo nombre y fecha de nacimiento
    $sql_check = "SELECT ID_DM FROM dm WHERE Nombre = ? AND Fecha_Nac = ? LIMIT 1";
    $stmt = $conn->prepare($sql_check); // Statement preparado para seguridad
    $stmt->bind_param('ss', $dm_nombre, $dm_fecha_nac); // Enlazar parámetros (s=string, s=string)
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc(); // Ya existe: usar el ID encontrado
        $dm_id_to_use = $row['ID_DM'];
    } else {
        // No existe: crear nuevo registro de DM con Fecha_Alt = NOW() (marca de alta)
        $sql_insert_dm = "INSERT INTO dm (Nombre, Fecha_Nac, Fecha_Alt) VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql_insert_dm);
        $stmt2->bind_param('ss', $dm_nombre, $dm_fecha_nac);

        // Ejecutar inserción y validar posibles fallos
        if (!$stmt2->execute()) {
            $db->cerrar();
            header("Location: ../crear_partida.php?error=" . urlencode("Error al crear DM."));
            exit;
        }

        $dm_id_to_use = $stmt2->insert_id; // Obtener el ID autogenerado del nuevo DM para usarlo en la partida
    }
} else {
    $dm_id_to_use = intval($ID_DM); // El usuario seleccionó un DM existente: normalizar y validar el ID
    if ($dm_id_to_use <= 0) {
        $db->cerrar();
        header("Location: ../crear_partida.php?error=" . urlencode("DM inválido."));
        exit;
    }
}

/**************************
* INSERCIÓN DE LA PARTIDA *
**************************/

// Preparar inserción con todos los campos requeridos.
$sql_insert_partida = "INSERT INTO partida (ID_Usuarios, ID_Sistema, ID_DM, Titulo, Fecha_Inic, Horario, Periocidad, No_Jugadores)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmtp = $conn->prepare($sql_insert_partida);

// Enlazar parámetros según sus tipos:
// i = integer, s = string. Orden debe coincidir con la consulta.
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

// Ejecutar la inserción y capturar errores en caso de fallo
if (!$stmtp->execute()) {
    // Redirigir con detalle del error del servidor (útil para depuración controlada)
    $db->cerrar();
    header("Location: ../crear_partida.php?error=" . urlencode("Error al crear partida: " . $conn->error));
    exit;
}

/*********************
* RESPUESTA DE ÉXITO *
*********************/

$db->cerrar();

header("Location: ../ver_partida.php?created=1"); // Si todo salió bien, redirigir a la vista de confirmación/listado.
exit; // Finalizar el script para evitar cualquier salida adicional