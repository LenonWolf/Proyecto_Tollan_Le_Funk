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
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos de inclusiones -->
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico"> <!-- Icono de la página -->
    <title>Tollan le Funk - Edición</title> <!-- Titulo de la página -->
    
    <style>
        /* Estilos para feedback visual */
        .loading-opacity {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <?php
    include 'src/conexion.php';
    include 'src/includes/header.php';
    ?>

    <main>
        <div>
            <h1>Editor de Partidas</h1>
        </div>

        <div class="table-scroll">
            <table aria-label="Lista para editar partidas de rol">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titulo</th>
                        <th>Sistema</th>
                        <th>Fecha Inicio</th>
                        <th>DM</th>
                        <th>Estado</th>
                        <th colspan="4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-partidas">
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
                    
                    $result = $conn->query($sql);
                    date_default_timezone_set('America/Mexico_City');
                    $hoy = date('Y-m-d H:i:s');

                    // Función para generar dinámicamente un botón de acción.
                    function generarBoton($estado, $id, $accion, $icono, $texto, $deshabilitados = []) {
                        $disabled = in_array($estado, $deshabilitados) ? 'disabled' : '';
                        return "<button class='btn-edit' data-id='$id' data-accion='$accion' $disabled>
                                    <i class='fas fa-$icono'></i> $texto
                                </button>";
                    }

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            $estado = $row['Estado'];
                            $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario'];
                            $fecha_fin = $row['Fecha_Fin'];

                            // Determinar estado si no está definido en la base de datos
                            if (empty($estado)) {
                                $estado = ($fecha_inicio > $hoy) ? 'Nueva' :
                                        ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                            }

                            // Sanitizar los datos
                            $id = htmlspecialchars($row['ID_Partida']);
                            $titulo = htmlspecialchars($row['Titulo']);
                            $sistema = htmlspecialchars($row['Sistema_Titulo']);
                            $dm = htmlspecialchars($row['DM_Nombre']);
                            $estado_safe = htmlspecialchars($estado);
                            $fecha_inicio_fmt = date("d/m/Y", strtotime($row['Fecha_Inicio']));

                            /*************************************
                            * Generación de botones según estado *
                            *************************************/

                            // Botón Pausar/Reanudar
                            if ($estado === "Pausada") {
                                $btnPausar = generarBoton($estado, $id, 'reanudar', 'play', 'Reanudar');
                            } else {
                                $btnPausar = generarBoton($estado, $id, 'pausar', 'pause', 'Pausar', ['Finalizada', 'Nueva', 'Cancelada']);
                            }

                            // Botón Finalizar
                            $btnFinalizar = generarBoton($estado, $id, 'finalizar', 'check', 'Finalizar', ['Finalizada', 'Cancelada', 'Nueva']);

                            // Botón Cancelar
                            $btnCancelar = generarBoton($estado, $id, 'cancelar', 'ban', 'Cancelar', ['Finalizada', 'Cancelada']);

                            // Botón Modificar
                            if (in_array($estado, ['Finalizada', 'Cancelada'])) {
                                $btnModificar = "<a class='btn-edit disabled'><i class='fas fa-edit'></i> Modificar</a>";
                            } else {
                                $btnModificar = "<a class='btn-edit' href='src/modificar_partida.php?id=$id' data-modificar='$id'>
                                                    <i class='fas fa-edit'></i> Modificar
                                                </a>";
                            }

                            // Imprimir la fila con atributo data-estado para Vue
                            echo "<tr data-partida-id='$id' data-estado='$estado_safe'>
                                    <td>$id</td>
                                    <td>$titulo</td>
                                    <td>$sistema</td>
                                    <td>$fecha_inicio_fmt</td>
                                    <td>$dm</td>
                                    <td class='estado-cell'>$estado_safe</td>
                                    <td class='btn-pausar-cell'>$btnPausar</td>
                                    <td class='btn-finalizar-cell'>$btnFinalizar</td>
                                    <td class='btn-cancelar-cell'>$btnCancelar</td>
                                    <td class='btn-modificar-cell'>$btnModificar</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No hay partidas registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>

    <!-- Vue.js 3 CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>
    
    <!-- Script externo para la funcionalidad Vue -->
    <script src="assets/js/editar_partida.js"></script>
</body>
</html>