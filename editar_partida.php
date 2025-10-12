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
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico">
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

                    $result = $conn->query($sql);
                    date_default_timezone_set('America/Mexico_City');
                    $hoy = date('Y-m-d H:i:s');

                    // Genera dinámicamente un botón (o deshabilitado) según el estado.
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

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            // Determinar estado dinámico
                            $estado = $row['Estado'];
                            $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario'];
                            $fecha_fin = $row['Fecha_Fin'];

                            if (empty($estado)) {
                                $estado = ($fecha_inicio > $hoy) ? 'Nueva' :
                                        ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                            }

                            // Sanitizar datos
                            $id = htmlspecialchars($row['ID_Partida']);
                            $titulo = htmlspecialchars($row['Titulo']);
                            $sistema = htmlspecialchars($row['Sistema_Titulo']);
                            $dm = htmlspecialchars($row['DM_Nombre']);
                            $estado_safe = htmlspecialchars($estado);
                            $fecha_inicio_fmt = date("d/m/Y", strtotime($row['Fecha_Inicio']));

                            // Botones dinámicos
                            if ($estado === "Pausada") {
                                $btnPausar = generarBoton($estado, $id, 'reanudar', 'play', 'Reanudar');
                            } else {
                                $btnPausar = generarBoton($estado, $id, 'pausar', 'pause', 'Pausar', ['Finalizada', 'Nueva', 'Cancelada']);
                            }

                            $btnFinalizar = generarBoton($estado, $id, 'finalizar', 'check', 'Finalizar', ['Finalizada', 'Cancelada', 'Nueva']);
                            $btnCancelar  = generarBoton($estado, $id, 'cancelar', 'ban', 'Cancelar', ['Finalizada', 'Cancelada']);

                            // Botón Modificar
                            if (in_array($estado, ['Finalizada', 'Cancelada'])) {
                                $btnModificar = "<a class='btn-edit disabled'><i class='fas fa-edit'></i> Modificar</a>";
                            } else {
                                $btnModificar = "
                                    <a class='btn-edit' href='src/modificar_partida.php?id=$id'
                                    onclick=\"window.open(this.href,'modificar','width=1000,height=600,scrollbars=yes'); return false;\">
                                        <i class='fas fa-edit'></i> Modificar
                                    </a>";
                            }

                            // Fila final
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
                    } else {
                        echo "<tr><td colspan='10'>No hay partidas registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
</body>
</html>