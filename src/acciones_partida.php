<?php
// Recibir acciones vía POST y actualizar el estado de la partida correspondiente en la base de datos

include 'conexion.php'; // Incluir la conexión a MySQL

/**************************************
* VALIDACIÓN DE PARÁMETROS DE ENTRADA *
**************************************/

// Verificar que se haya enviado una acción y un ID por POST.
// 'accion' define la operación a realizar (pausar, reanudar, finalizar, cancelar).
// 'id' identifica la partida sobre la que se aplicará la acción.
if (isset($_POST['accion']) && isset($_POST['id'])) {

    $accion = $_POST['accion'];// Extraer la acción solicitada
    $id = intval($_POST['id']); // Normalizar el ID a entero para evitar inyección por tipo

    /********************
    * CONTEXTO TEMPORAL *
    ********************/

    date_default_timezone_set('America/Mexico_City'); // Fijar la zona horaria a Ciudad de México

    $hoy = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

    /*********************************
    * SELECCIÓN DE ACCIÓN A EJECUTAR *
    *********************************/

    // Según el valor de $accion, construir el SQL correspondiente y ejecutarlo.    
    switch ($accion) {
        case 'pausar':
            // PAUSAR: establece el estado explícito en 'Pausada'.
            $stmt = $conn->prepare("UPDATE partida SET Estado = 'Pausada' WHERE ID_Partida = ?"); // Preparar la consulta con un marcador (?) para el ID
            $stmt->bind_param("i", $id); // Enlazar el parámetro (i = integer)
            $stmt->execute(); // Ejecutar la consulta
            $stmt->close(); // Cerrar el statement
            break;

        case 'reanudar':
            // REANUDAR: limpia el campo Estado para permitir que la lógica dinámica del listado determine si está 'Nueva' o 'Activa'.
            $stmt = $conn->prepare("UPDATE partida SET Estado = NULL WHERE ID_Partida = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'finalizar':
            // FINALIZAR: limpia Estado y fija Fecha_Fin a la marca temporal actual. Con esto, la lógica del listado interpretará que ya terminó.
            $stmt = $conn->prepare("UPDATE partida SET Estado = NULL, Fecha_Fin = ? WHERE ID_Partida = ?");
            $stmt->bind_param("si", $hoy, $id); // Enlazar los parámetros: Fecha_Fin (string) y ID (integer)
            $stmt->execute();
            $stmt->close();
            break;

        case 'cancelar':
            // CANCELAR: establece Estado en 'Cancelada' y fija Fecha_Fin (momento de cancelación).
            $stmt = $conn->prepare("UPDATE partida SET Estado = 'Cancelada', Fecha_Fin = ? WHERE ID_Partida = ?");
            $stmt->bind_param("si", $hoy, $id);
            $stmt->execute();
            $stmt->close();
            break;
    }
}

/**************************
* REDIRECCIÓN POST-ACCIÓN *
**************************/

// Tras ejecutar la acción, redirigir de vuelta al editor de partidas.
// Esto evita reenvíos de formularios y refresca la vista con el nuevo estado.
header("Location: ../editar_partida.php");
exit; // Terminar el script para asegurar que no se envía más contenido