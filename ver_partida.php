<!DOCTYPE html> <!-- HTML5 -->
<html lang="es"> <!-- HTML en español -->
    <head> <!-- Metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores -->
    <meta name="description" content="Página web de visualización de juegos de rol de la cafetería Tollan le Funk"> <!-- Descripción -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración de vista para dispositivos móviles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css"> <!-- Estilos globales -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos para elementos incluidos -->
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico"> <!-- Icono de la pestaña -->
    <title>Tollan le Funk - Visualización</title> <!-- Titulo de la página -->
</head>

<body> <!-- Cuerpo del documento -->
    <?php
    include 'src/conexion.php'; // Incluir la conexión a la base de datos
    include 'src/includes/header.php'; // Incluir el encabezado desde un archivo externo
    ?>

    <main> <!-- Contenido principal de la página -->
        <div>
            <h1>Visualizador de Partidas</h1> <!-- Título principal de la página -->
        </div>
        
        <div>
            <h2 id="h-ver" class="h-ver-c">Bienvenido al visualizador de partidas</h2> <!-- Subtítulo -->
            <div class="table-scroll"> <!-- Contenedor con desplazamiento para la tabla -->
                <table aria-label="Listado de partidas de rol registradas"> <!-- Tabla para mostrar las partidas -->
                    <thead> <!-- Encabezado de la tabla -->
                        <tr> <!-- Fila del encabezado -->
                            <th>ID</th>
                            <th>Titulo</th>
                            <th>Sistema</th>
                            <th>Descripción</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Horario</th>
                            <th>Periocidad</th>
                            <th>DM</th>
                            <th>Jugadores</th>
                            <th>Clasificación</th>
                            <th>Tipo</th>
                            <th>Estado</th>
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
                                    sistema.Descripcion AS Sistema_Descripcion,
                                    partida.Fecha_Inic AS Fecha_Inicio,
                                    partida.Fecha_Fin AS Fecha_Fin,
                                    partida.Horario,
                                    partida.Periocidad,
                                    dm.Nombre AS DM_Nombre,
                                    partida.No_Jugadores AS Numero_Jugadores,
                                    sistema.Clasificacion,
                                    tipo.Tipo,
                                    partida.Estado
                                FROM partida
                                    INNER JOIN sistema ON partida.ID_Sistema = sistema.ID_Sistema
                                    INNER JOIN dm ON partida.ID_DM = dm.ID_DM
                                    INNER JOIN tipo ON sistema.ID_Tipo = tipo.ID_Tipo
                                ORDER BY partida.ID_Partida ASC
                                ";
                        
                        /******************************
                        * Procesamiento de resultados *
                        ******************************/

                        $result = $conn->query($sql); // Ejecutar la consulta y obtener resultados
                        date_default_timezone_set('America/Mexico_City'); // Establecer zona horaria
                        $hoy = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual

                        if ($result->num_rows > 0) { // Verificar si hay resultados
                            while($row = $result->fetch_assoc()) { // Procesar cada fila de resultados
                                
                                $estado = $row['Estado']; // Obtener el estado actual
                                $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario']; // Fecha y hora de inicio
                                $fecha_fin = $row['Fecha_Fin']; // Usada para calcular si la partida sigue activa

                                // Determinar estado si no está definido
                                // Lógica de estado:
                                // - Nueva: aún no inicia
                                // - Activa: ya inició y no ha terminado
                                // - Finalizada: ya terminó
                                if (empty($estado)) {
                                    $estado = ($fecha_inicio > $hoy) ? 'Nueva' :
                                            ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                                }

                                // Formatear fechas y horas para mejor legibilidad
                                $fecha_inicio_fmt = !empty($row['Fecha_Inicio'])
                                    ? date("d/m/Y", strtotime($row['Fecha_Inicio']))
                                    : '';

                                $fecha_fin_fmt = !empty($row['Fecha_Fin'])
                                    ? date("d/m/Y H:i", strtotime($row['Fecha_Fin']))
                                    : '--/--/---- --:--';

                                $horario_fmt = !empty($row['Horario'])
                                    ? date("H:i", strtotime($row['Horario']))
                                    : '';

                                // Mostrar la fila de la tabla con datos sanitizados
                                echo "<tr>
                                        <td>".htmlspecialchars($row['ID_Partida'])."</td>
                                        <td>".htmlspecialchars($row['Titulo'])."</td>
                                        <td>".htmlspecialchars($row['Sistema_Titulo'])."</td>
                                        <td>".htmlspecialchars($row['Sistema_Descripcion'])."</td>
                                        <td>".htmlspecialchars($fecha_inicio_fmt)."</td>
                                        <td>".htmlspecialchars($fecha_fin_fmt)."</td>
                                        <td>".htmlspecialchars($horario_fmt)."</td>
                                        <td>".htmlspecialchars($row['Periocidad'])."</td>
                                        <td>".htmlspecialchars($row['DM_Nombre'])."</td>
                                        <td>".htmlspecialchars($row['Numero_Jugadores'])."</td>
                                        <td>".htmlspecialchars($row['Clasificacion'])."</td>
                                        <td>".htmlspecialchars($row['Tipo'])."</td>
                                        <td>".htmlspecialchars($estado)."</td>
                                    </tr>";
                            }
                        } else { // Si no hay resultados
                            echo "<tr><td colspan='13'>No hay partidas registradas.</td></tr>"; // Fila indicando que no hay datos
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?> <!-- Incluir el pie de página desde un archivo externo -->
</body>
</html>