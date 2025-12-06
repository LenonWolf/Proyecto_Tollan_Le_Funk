<?php
// Verificar permisos antes de procesar
require_once 'check_auth.php';
verificarPermisos(['Adm', 'Mod']);

require_once 'conexion.php';

$db = new Conexion('usr_edit_partida', 'edit_partida123');
$conn = $db->conectar();

$esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (isset($_POST['accion']) && isset($_POST['id'])) {

    $accion = $_POST['accion'];
    $id = intval($_POST['id']);

    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d H:i:s');

    $success = false;
    $mensaje = '';
    $nuevoEstado = '';

    switch ($accion) {
        case 'pausar':
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
            $stmt = $conn->prepare("UPDATE partida SET Estado = NULL WHERE ID_Partida = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success = true;
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

    if ($esAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'nuevoEstado' => $nuevoEstado,
            'mensaje' => $mensaje,
            'id' => $id
        ]);
        exit;
    } else {
        header("Location: " . url('editar_partida.php'));
        exit;
    }

} else {
    if ($esAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'mensaje' => 'Parámetros incompletos'
        ]);
    } else {
        header("Location: " . url('editar_partida.php'));
    }
    exit;
}