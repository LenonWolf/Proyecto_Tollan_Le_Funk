<?php
// Recibir acciones vía POST y actualizar el estado de la partida correspondiente en la base de datos

require_once 'conexion.php';

// Crear conexión con usuario de solo lectura
$db = new Conexion('usr_edit_partida', 'edit_partida123');
$conn = $db->conectar();

/**************************************
* VALIDACIÓN DE PARÁMETROS DE ENTRADA *
**************************************/

// Detectar si la petición es AJAX (para responder con JSON)
$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verificar que se haya enviado una acción y un ID por POST.
if (isset($_POST['accion']) && isset($_POST['id'])) {

    $accion = $_POST['accion']; // Extraer la acción solicitada
    $id = intval($_POST['id']); // Normalizar el ID a entero para evitar inyección

    /********************
    * CONTEXTO TEMPORAL *
    ********************/

    date_default_timezone_set('America/Mexico_City'); // Fijar la zona horaria a Ciudad de México
    $hoy = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

    /*********************************
    * VARIABLES DE RESPUESTA *
    *********************************/
    
    $success = false;
    $mensaje = '';
    $nuevoEstado = '';

    /*********************************
    * SELECCIÓN DE ACCIÓN A EJECUTAR *
    *********************************/

    switch ($accion) {
        case 'pausar':
            // PAUSAR: establece el estado explícito en 'Pausada'.
            $stmt = $conn->prepare("UPDATE partida SET Estado = 'Pausada' WHERE ID_Partida = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success = true;
                $nuevoEstado = 'Pausada';
                $mensaje = 'Partida pausada correctamente';
            } else {
                $mensaje = 'Error al pausar la partida';
            }
            $stmt->close();
            break;

        case 'reanudar':
            // REANUDAR: limpia el campo Estado para permitir que la lógica dinámica determine si está 'Nueva' o 'Activa'.
            $stmt = $conn->prepare("UPDATE partida SET Estado = NULL WHERE ID_Partida = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success = true;
                // Determinar el nuevo estado basado en fechas
                $stmt2 = $conn->prepare("SELECT Fecha_Inic, Horario, Fecha_Fin FROM partida WHERE ID_Partida = ?");
                $stmt2->bind_param("i", $id);
                $stmt2->execute();
                $result = $stmt2->get_result();
                $row = $result->fetch_assoc();
                
                $fecha_inicio = $row['Fecha_Inic'] . ' ' . $row['Horario'];
                $fecha_fin = $row['Fecha_Fin'];
                
                $nuevoEstado = ($fecha_inicio > $hoy) ? 'Nueva' :
                              ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                
                $mensaje = 'Partida reanudada correctamente';
                $stmt2->close();
            } else {
                $mensaje = 'Error al reanudar la partida';
            }
            $stmt->close();
            break;

        case 'finalizar':
            // FINALIZAR: limpia Estado y fija Fecha_Fin a la marca temporal actual.
            $stmt = $conn->prepare("UPDATE partida SET Estado = NULL, Fecha_Fin = ? WHERE ID_Partida = ?");
            $stmt->bind_param("si", $hoy, $id);
            if ($stmt->execute()) {
                $success = true;
                $nuevoEstado = 'Finalizada';
                $mensaje = 'Partida finalizada correctamente';
            } else {
                $mensaje = 'Error al finalizar la partida';
            }
            $stmt->close();
            break;

        case 'cancelar':
            // CANCELAR: establece Estado en 'Cancelada' y fija Fecha_Fin.
            $stmt = $conn->prepare("UPDATE partida SET Estado = 'Cancelada', Fecha_Fin = ? WHERE ID_Partida = ?");
            $stmt->bind_param("si", $hoy, $id);
            if ($stmt->execute()) {
                $success = true;
                $nuevoEstado = 'Cancelada';
                $mensaje = 'Partida cancelada correctamente';
            } else {
                $mensaje = 'Error al cancelar la partida';
            }
            $stmt->close();
            break;

        default:
            $mensaje = 'Acción no válida';
            break;
    }

    /***********************************
    * RESPUESTA SEGÚN TIPO DE PETICIÓN *
    ***********************************/

    if ($esAjax) {
        // Respuesta JSON para peticiones AJAX (Vue.js)
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'nuevoEstado' => $nuevoEstado,
            'mensaje' => $mensaje,
            'id' => $id
        ]);
        exit;
    } else {
        // Redirección tradicional para compatibilidad con formularios normales
        header("Location: ../editar_partida.php");
        exit;
    }

} else {
    // Si no se recibieron los parámetros necesarios
    if ($esAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'mensaje' => 'Parámetros incompletos'
        ]);
    } else {
        header("Location: ../editar_partida.php");
    }
    exit;
}