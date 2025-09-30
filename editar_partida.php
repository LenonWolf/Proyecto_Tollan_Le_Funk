<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de edición de juegos de rol de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/img/dragon.ico">
    <title>Tollan le Funk - Edición</title>
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
            <table>
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
                <tbody>
                    <?php
                    // Consulta para obtener las partidas
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

                            // Botón de pausa/despausa
                            $btnPausar = "";
                            if ($estado == "Finalizada" || $estado == "Nueva" || $estado == "Cancelada") {
                                $btnPausar = "<button class='btn-edit' disabled><i class='fas fa-pause'></i> Pausar</button>";
                            } elseif ($estado == "Pausada") {
                                $btnPausar = "
                                    <form method='POST' action='src/acciones_partida.php' style='display:inline;'>
                                        <input type='hidden' name='id' value='".$row['ID_Partida']."'>
                                        <input type='hidden' name='accion' value='reanudar'>
                                        <button class='btn-edit' type='submit'><i class='fas fa-play'></i> Reanudar</button>
                                    </form>";
                            } else { // Activa u otro
                                $btnPausar = "
                                    <form method='POST' action='src/acciones_partida.php' style='display:inline;'>
                                        <input type='hidden' name='id' value='".$row['ID_Partida']."'>
                                        <input type='hidden' name='accion' value='pausar'>
                                        <button class='btn-edit' type='submit'><i class='fas fa-pause'></i> Pausar</button>
                                    </form>";
                            }

                            // Botón finalizar
                            $btnFinalizar = "";
                            if ($estado == "Finalizada" || $estado == "Cancelada" || $estado == "Nueva") {
                                $btnFinalizar = "<button class='btn-edit' disabled><i class='fas fa-check'></i> Finalizar</button>";
                            } else {
                                $btnFinalizar = "
                                    <form method='POST' action='src/acciones_partida.php' style='display:inline;'>
                                        <input type='hidden' name='id' value='".$row['ID_Partida']."'>
                                        <input type='hidden' name='accion' value='finalizar'>
                                        <button class='btn-edit' type='submit'><i class='fas fa-check'></i> Finalizar</button>
                                    </form>";
                            }

                            // Botón cancelar
                            $btnCancelar = "";
                            if ($estado == "Finalizada" || $estado == "Cancelada") {
                                $btnCancelar = "<button class='btn-edit' disabled><i class='fas fa-ban'></i> Cancelar</button>";
                            } else {
                                $btnCancelar = "
                                    <form method='POST' action='src/acciones_partida.php' style='display:inline;'>
                                        <input type='hidden' name='id' value='".$row['ID_Partida']."'>
                                        <input type='hidden' name='accion' value='cancelar'>
                                        <button class='btn-edit' type='submit'><i class='fas fa-ban'></i> Cancelar</button>
                                    </form>";
                            }

                            // Botón modificar
                            if ($estado == "Finalizada" || $estado == "Cancelada") {
                                $btnModificar = "<a class='btn-edit disabled'><i class='fas fa-edit'></i> Modificar</a>";
                            } else {
                                $btnModificar = "
                                <a class='btn-edit' href='src/modificar_partida.php?id=".$row['ID_Partida']."'
                                onclick=\"window.open(this.href,'modificar','width=1000,height=600,scrollbars=yes'); return false;\">
                                    <i class='fas fa-edit'></i> Modificar
                                </a>";
                            }

                            $fecha_inicio_fmt = date("d/m/Y", strtotime($row['Fecha_Inicio']));

                            echo "<tr>
                                    <td>".htmlspecialchars($row['ID_Partida'])."</td>
                                    <td>".htmlspecialchars($row['Titulo'])."</td>
                                    <td>".htmlspecialchars($row['Sistema_Titulo'])."</td>
                                    <td>".htmlspecialchars($fecha_inicio_fmt)."</td>
                                    <td>".htmlspecialchars($row['DM_Nombre'])."</td>
                                    <td>".htmlspecialchars($estado)."</td>
                                    <td>".$btnPausar."</td>
                                    <td>".$btnFinalizar."</td>
                                    <td>".$btnCancelar."</td>
                                    <td>".$btnModificar."</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No hay partidas registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
</body>
</html>