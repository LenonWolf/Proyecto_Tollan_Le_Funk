<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de visualización de juegos de rol de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/img/dragon.ico">
    <title>Tollan le Funk - Visualización</title>
</head>

<body>
    <?php
    include 'src/conexion.php';
    include 'src/includes/header.php';
    ?>

    <main>
        <div>
            <h1>Visualizador de Partidas</h1>
        </div>
        
        <div>
            <h2 id="h-ver" class="h-ver-c">Bienvenido al visualizador de partidas</h2>
            <div class="table-scroll">
                <table aria-label="Listado de partidas de rol registradas">
                    <thead>
                        <tr>
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
                    <tbody>
                        <?php
                        // Consulta para obtener las partidas
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

                        $result = $conn->query($sql);
                        date_default_timezone_set('America/Mexico_City');
                        $hoy = date('Y-m-d H:i:s');

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                // Estado dinamico
                                $estado = $row['Estado'];
                                $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario'];
                                $fecha_fin = $row['Fecha_Fin'];

                                if(is_null($estado) || $estado == '') {
                                    if($fecha_inicio > $hoy) {
                                        $estado = 'Nueva';
                                    } elseif(is_null($fecha_fin) || $fecha_fin > $hoy) {
                                        $estado = 'Activa';
                                    } else {
                                        $estado = 'Finalizada';
                                    }
                                }

                                echo "<tr>
                                        <td>".htmlspecialchars($row['ID_Partida'])."</td>
                                        <td>".htmlspecialchars($row['Titulo'])."</td>
                                        <td>".htmlspecialchars($row['Sistema_Titulo'])."</td>
                                        <td>".htmlspecialchars($row['Sistema_Descripcion'])."</td>
                                        <td>".htmlspecialchars($row['Fecha_Inicio'])."</td>
                                        <td>".htmlspecialchars($row['Fecha_Fin'])."</td>
                                        <td>".htmlspecialchars($row['Horario'])."</td>
                                        <td>".htmlspecialchars($row['Periocidad'])."</td>
                                        <td>".htmlspecialchars($row['DM_Nombre'])."</td>
                                        <td>".htmlspecialchars($row['Numero_Jugadores'])."</td>
                                        <td>".htmlspecialchars($row['Clasificacion'])."</td>
                                        <td>".htmlspecialchars($row['Tipo'])."</td>
                                        <td>".htmlspecialchars($estado)."</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='13'>No hay partidas registradas.</td></tr>";
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
</body>
</html>