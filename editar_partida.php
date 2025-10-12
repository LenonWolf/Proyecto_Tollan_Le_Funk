<!DOCTYPE html> <!-- HTML5 -->
<html lang="es"> <!-- HTML en español -->
<head> <!-- Metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores -->
    <meta name="description" content="Página web de edición de juegos de rol de la cafetería Tollan le Funk"> <!-- Descripción -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración de vista para dispositivos móviles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css"> <!-- Estilos globales -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css"> <!-- Estilos de botones -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos para elementos incluidos -->
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico"> <!-- Icono de la pestaña -->
    <title>Tollan le Funk - Edición</title> <!-- Titulo de la página -->
</head>

<body> <!-- Cuerpo del documento -->
    <?php
    include 'src/conexion.php'; // Incluir la conexión a la base de datos
    include 'src/includes/header.php'; // Incluir el encabezado desde un archivo externo
    ?>

    <main> <!-- Contenido principal de la página -->
        <div>
            <h1>Editor de Partidas</h1> <!-- Título principal de la página -->
        </div>

        <div class="table-scroll"> <!-- Contenedor con desplazamiento para la tabla -->
            <table aria-label="Lista para editar partidas de rol"> <!-- Tabla para editar las partidas -->
                <thead> <!-- Encabezado de la tabla -->
                    <tr> <!-- Fila del encabezado -->
                        <th>ID</th>
                        <th>Titulo</th>
                        <th>Sistema</th>
                        <th>Fecha Inicio</th>
                        <th>DM</th>
                        <th>Estado</th>
                        <th colspan="4">Acciones</th> <!-- Columna para los botones de acción -->
                    </tr>
                </thead>
                <tbody> <!-- Cuerpo de la tabla -->
                    <?php

                    /***********************************************************************
                    * Consulta SQL para obtener las partidas con sus detalles relacionados *
                    ***********************************************************************/
                    
                    $sql = "SELECT 
                                partida.ID_Partida, 
                                partida.Titulo, 
                                sistema.Titulo AS Sistema_Titulo,
                                partida.Fecha_Inic AS Fecha_Inicio,
                                partida.Fecha_Fin AS Fecha_Fin,
                                partida.Horario,
                                dm.Nombre AS DM_Nombre,
                                partida.Estado
                            FROM partida
                                INNER JOIN sistema ON partida.ID_Sistema = sistema.ID_Sistema
                                INNER JOIN dm ON partida.ID_DM = dm.ID_DM
                            ORDER BY partida.ID_Partida ASC";

                    /******************************
                    * Procesamiento de resultados *
                    ******************************/
                    
                    $result = $conn->query($sql); // Ejecutar la consulta y obtener resultados
                    date_default_timezone_set('America/Mexico_City'); // Establecer zona horaria
                    $hoy = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

                    // Función para generar dinámicamente un botón de acción.
                    // Si el estado está en la lista de deshabilitados, devuelve un botón inactivo.
                    // En caso contrario, devuelve un formulario con un botón activo que envía la acción correspondiente.
                    function generarBoton($estado, $id, $accion, $icono, $texto, $deshabilitados = []) {
                        if (in_array($estado, $deshabilitados)) {
                            return "<button class='btn-edit' disabled><i class='fas fa-$icono'></i> $texto</button>";
                        }
                        return "
                            <form method='POST' action='src/acciones_partida.php' style='display:inline;'>
                                <input type='hidden' name='id' value='$id'>
                                <input type='hidden' name='accion' value='$accion'>
                                <button class='btn-edit' type='submit'><i class='fas fa-$icono'></i> $texto</button>
                            </form>";
                    }

                    if ($result->num_rows > 0) { // Verificar si hay resultados
                        while ($row = $result->fetch_assoc()) { // Procesar cada fila de resultados

                            $estado = $row['Estado']; // Obtener el estado actual de la partida
                            $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario']; // Concatenar fecha y hora de inicio
                            $fecha_fin = $row['Fecha_Fin']; // Fecha de fin de la partida

                            // Determinar estado si no está definido en la base de datos
                            // Lógica de estado:
                            // - Nueva: aún no inicia
                            // - Activa: ya inició y no ha terminado
                            // - Finalizada: ya terminó
                            if (empty($estado)) {
                                $estado = ($fecha_inicio > $hoy) ? 'Nueva' :
                                        ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                            }

                            // Sanitizar los datos para evitar inyección de HTML
                            $id = htmlspecialchars($row['ID_Partida']);
                            $titulo = htmlspecialchars($row['Titulo']);
                            $sistema = htmlspecialchars($row['Sistema_Titulo']);
                            $dm = htmlspecialchars($row['DM_Nombre']);
                            $estado_safe = htmlspecialchars($estado);
                            $fecha_inicio_fmt = date("d/m/Y", strtotime($row['Fecha_Inicio'])); // Formato legible de fecha

                            /*************************************
                            * Generación de botones según estado *
                            *************************************/

                            // Botón Pausar/Reanudar: cambia según el estado actual
                            if ($estado === "Pausada") {
                                $btnPausar = generarBoton($estado, $id, 'reanudar', 'play', 'Reanudar');
                            } else {
                                $btnPausar = generarBoton($estado, $id, 'pausar', 'pause', 'Pausar', ['Finalizada', 'Nueva', 'Cancelada']);
                            }

                            // Botón Finalizar: deshabilitado si ya está finalizada, cancelada o aún no inicia
                            $btnFinalizar = generarBoton($estado, $id, 'finalizar', 'check', 'Finalizar', ['Finalizada', 'Cancelada', 'Nueva']);

                            // Botón Cancelar: deshabilitado si ya está finalizada o cancelada
                            $btnCancelar  = generarBoton($estado, $id, 'cancelar', 'ban', 'Cancelar', ['Finalizada', 'Cancelada']);

                            // Botón Modificar: solo disponible si la partida no está finalizada ni cancelada
                            if (in_array($estado, ['Finalizada', 'Cancelada'])) {
                                $btnModificar = "<a class='btn-edit disabled'><i class='fas fa-edit'></i> Modificar</a>";
                            } else {
                                $btnModificar = "
                                    <a class='btn-edit' href='src/modificar_partida.php?id=$id'
                                    onclick=\"window.open(this.href,'modificar','width=1000,height=600,scrollbars=yes'); return false;\">
                                        <i class='fas fa-edit'></i> Modificar
                                    </a>";
                            }

                            // Imprimir la fila final de la tabla con todos los datos y botones
                            echo "<tr>
                                    <td>$id</td>
                                    <td>$titulo</td>
                                    <td>$sistema</td>
                                    <td>$fecha_inicio_fmt</td>
                                    <td>$dm</td>
                                    <td>$estado_safe</td>
                                    <td>$btnPausar</td>
                                    <td>$btnFinalizar</td>
                                    <td>$btnCancelar</td>
                                    <td>$btnModificar</td>
                                </tr>";
                        }
                    } else { // Si no hay resultados en la consulta
                        echo "<tr><td colspan='10'>No hay partidas registradas.</td></tr>"; // Mensaje de tabla vacía
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?> <!-- Incluir el pie de página desde un archivo externo -->
</body>
</html>