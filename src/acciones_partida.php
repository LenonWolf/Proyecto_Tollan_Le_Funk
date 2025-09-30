<?php
include 'conexion.php';

if (isset($_POST['accion']) && isset($_POST['id'])) {
    $accion = $_POST['accion'];
    $id = intval($_POST['id']);

    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d H:i:s');

    switch ($accion) {
        case 'pausar':
            $sql = "UPDATE partida SET Estado = 'Pausada' WHERE ID_Partida = $id";
            $result = mysqli_query($conn, $sql);
            break;
        case 'reanudar':
            $sql = "UPDATE partida SET Estado = NULL WHERE ID_Partida = $id";
            $result = mysqli_query($conn, $sql);
            break;
        case "finalizar":
            $sql = "UPDATE partida SET Estado = NULL, Fecha_Fin = '$hoy' WHERE ID_Partida = $id";
            $result = mysqli_query($conn, $sql);
            break;
        case 'cancelar':
            $sql = "UPDATE partida SET Estado = 'Cancelada', Fecha_Fin = '$hoy' WHERE ID_Partida = $id";
            $result = mysqli_query($conn, $sql);
            break;
    }
}

header("Location: ../editar_partida.php");
exit;
