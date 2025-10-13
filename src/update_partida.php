<?php
// Recibir datos por POST (y el ID por GET), validar entradas, (opcionalmente crear un nuevo DM) y actualizar la partida.

include 'conexion.php'; // Incluir la conexión a la base de datos

// Función auxiliar para obtener un campo de $_POST.
// - Si existe, recorta espacios con trim.
// - Si no existe, devuelve null.
// Evita repetir patrones de isset(...) y normaliza entradas.
function post($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : null; }

/***************************
* OBTENCIÓN DEL ID VÍA GET *
***************************/

// El ID de la partida a actualizar se recibe en la URL (?id=...).
// Se normaliza a entero para evitar problemas de tipo.
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

/******************************************
* EXTRACCIÓN DE PARÁMETROS DEL FORMULARIO *
******************************************/

$ID_Sistema = intval(post('ID_Sistema')); // Sistema seleccionado
$ID_DM = post('ID_DM'); // Puede ser ID existente o la cadena 'new'

// Posible NUEVO DM (solo se usa si el usuario eligió crear uno)
$dm_nombre = post('lbl-nombre'); // Nombre del nuevo DM
$dm_fecha_nac = post('lbl-fecha-nac'); // Fecha de nacimiento del nuevo DM (YYYY-MM-DD)

// Datos de la partida a actualizar
$Titulo = post('Titulo');
$Fecha_Inic = post('Fecha_Inic'); // Fecha (YYYY-MM-DD)
$Horario = post('Horario'); // Hora (HH:MM)
$Periocidad = post('Periocidad'); // Semanal, Quincenal, One_Shot
$No_Jugadores = intval(post('No_Jugadores')); // Entero >= 1

// Definir zona horaria y obtener la fecha actual (para restricciones en el formulario)
date_default_timezone_set('America/Mexico_City');
$hoy = date('Y-m-d');

/***********************
* VALIDACIONES BÁSICAS *
***********************/

// Se acumulan mensajes de error para mostrarlos todos a la vez en un alert.
$errors = [];
if (!$ID_Sistema) $errors[] = 'Seleccione un sistema.';
if (!$Titulo) $errors[] = 'Ingrese título.';
if (!$Fecha_Inic) $errors[] = 'Ingrese fecha de inicio.';
if (!$Horario) $errors[] = 'Ingrese horario.';
if (!$Periocidad) $errors[] = 'Seleccione periocidad.';
if ($No_Jugadores < 1) $errors[] = 'Número de jugadores inválido.';

// Si hay errores, se notifica al usuario mediante alert y se regresa a la pantalla anterior
if (!empty($errors)) {
    $msg = implode(' | ', $errors); // Unir errores con separador visual
    echo "
        <script>
            alert('Error: $msg'); window.history.back();
        </script>
        ";
    exit; // Detener ejecución para evitar continuar con lógica inválida
}

/*******************************************
* PROCESAMIENTO DEL DM (EXISTENTE O NUEVO) *
*******************************************/

// Si el usuario eligió crear un nuevo DM (o el select quedó vacío):
if ($ID_DM === 'new' || $ID_DM === '' ) {
    // Validar que se hayan proporcionado nombre y fecha de nacimiento del nuevo DM
    if (empty($dm_nombre) || empty($dm_fecha_nac)) {
        echo "
            <script>
                alert('Datos del DM incompletos.'); window.history.back();
            </script>
            ";
        exit;
    }

    // Verificar si ya existe un DM con el mismo nombre y fecha de nacimiento para reutilizarlo
    $sql_check = "SELECT ID_DM FROM dm WHERE Nombre = ? AND Fecha_Nac = ? LIMIT 1";
    $stmt = $conn->prepare($sql_check); // Statement preparado: evita inyección SQL
    $stmt->bind_param('ss', $dm_nombre, $dm_fecha_nac); // Enlazar parámetros (s=string, s=string)
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc(); // Ya existe un DM idéntico: usar su ID
        $dm_id_to_use = $row['ID_DM'];
    } else {
        // No existe: crear nuevo registro de DM (Fecha_Alt = NOW() como marca de alta)
        $sql_insert_dm = "INSERT INTO dm (Nombre, Fecha_Nac, Fecha_Alt) VALUES (?, ?, NOW())";
        $stmt2 = $conn->prepare($sql_insert_dm);
        $stmt2->bind_param('ss', $dm_nombre, $dm_fecha_nac);

        // Ejecutar e informar en caso de error
        if (!$stmt2->execute()) {
            echo "
                <script>
                    alert('Error al crear DM.'); window.history.back();
                </script>
            ";
            exit;
        }

        $dm_id_to_use = $stmt2->insert_id; // Guardar el ID autogenerado del nuevo DM para la actualización de la partida
    }
} else {
    // Se seleccionó un DM existente: normalizar y validar su ID
    $dm_id_to_use = intval($ID_DM);
    if ($dm_id_to_use <= 0) {
        echo "
            <script>
                alert('DM inválido.'); window.history.back();
            </script>
            ";
        exit;
    }
}

/******************************
* ACTUALIZACIÓN DE LA PARTIDA *
******************************/

// Preparar la sentencia de actualización con todos los campos relevantes.
// Se utiliza statement preparado para seguridad y consistencia de tipos.
$sql_update_partida = "UPDATE partida SET ID_Sistema = ?, ID_DM = ?, Titulo = ?, Fecha_Inic = ?, Horario = ?, Periocidad = ?, No_Jugadores = ?
                       WHERE ID_Partida = ?";
$stmtp = $conn->prepare($sql_update_partida);

// Enlazar parámetros con sus tipos correspondientes:
// i = integer, s = string. El orden debe coincidir con la consulta.
// Último parámetro ($id) es el ID de la partida a actualizar.
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

// Ejecutar y, si hay error, notificar al usuario con el detalle (escapado) y volver atrás
if (!$stmtp->execute()) {
    echo "
        <script>
            alert('Error al actualizar partida: " . addslashes($conn->error) . "'); window.history.back();
        </script>
        ";
    exit;
}

/******************************
* RESPUESTA DE ÉXITO (POP-UP) *
******************************/

// Si la actualización fue exitosa y esta ventana es un pop-up abierto por otra, se solicita refrescar la ventana padre (por ejemplo, editar_partida.php)
// y luego se cierra la ventana actual (modificar_partida.php).
echo "
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.location.reload(); // refresca la vista del editor
        }
        window.close(); // cierra esta ventana de modificación
    </script>
    ";
exit; // Finalizar el script para asegurar que no se envíe más contenido