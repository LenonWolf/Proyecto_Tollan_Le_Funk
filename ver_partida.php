<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_name('TOLLAN_SESSION');
    session_start();
}
?>
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
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico">
    <title>Tollan le Funk - Visualización</title>
</head>

<body>
    <?php
    // Incluir la clase de conexión
    require_once 'src/conexion.php';
    
    // Crear conexión con usuario de solo lectura
    $db = new Conexion('usr_lector', 'lector123');
    $conn = $db->conectar();
    
    include 'src/includes/header.php';
    
    // Determinar el tipo de usuario
    $tipoUsuario = isset($_SESSION['Tipo_Usr']) ? $_SESSION['Tipo_Usr'] : null;
    $esAdminOMod = ($tipoUsuario === 'Adm' || $tipoUsuario === 'Mod');
    ?>

    <main>
        <div>
            <h1>Visualizador de Partidas</h1>
        </div>
        
        <div>
            <?php if ($esAdminOMod): ?>
                <h2 id="h-ver" class="h-ver-c">Bienvenido al administrador de partidas</h2>
            <?php else: ?>
                <h2 id="h-ver" class="h-ver-c">Bienvenido al visualizador de partidas</h2>
            <?php endif; ?>
            
            <?php if ($esAdminOMod): ?>
                <!-- Filtros dinámicos para Admin/Mod -->
                <div style="margin: 20px 0; text-align: center;">
                    <label style="margin-right: 10px; font-weight: bold;">Filtrar por estado:</label>
                    <button class="filter-btn active" data-filter="all">Todas</button>
                    <button class="filter-btn" data-filter="Nueva">Nuevas</button>
                    <button class="filter-btn" data-filter="Activa">Activas</button>
                    <button class="filter-btn" data-filter="Pausada">Pausadas</button>
                    <button class="filter-btn" data-filter="Finalizada">Finalizadas</button>
                    <button class="filter-btn" data-filter="Cancelada">Canceladas</button>
                </div>
            <?php endif; ?>
            
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
                    <tbody id="tabla-partidas">
                        <?php
                        /***********************************************************************
                        * Consulta SQL DINÁMICA según el tipo de usuario
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
                                    INNER JOIN tipo ON sistema.ID_Tipo = tipo.ID_Tipo";
                        
                        // Si es usuario normal, filtrar solo partidas activas o nuevas
                        if (!$esAdminOMod) {
                            $sql .= " WHERE (partida.Estado = 'Activa' OR partida.Estado = 'Nueva' OR partida.Estado IS NULL)";
                        }
                        
                        $sql .= " ORDER BY partida.ID_Partida ASC";
                        
                        /******************************
                        * Procesamiento de resultados
                        ******************************/
                        
                        $result = $conn->query($sql);
                        date_default_timezone_set('America/Mexico_City');
                        $hoy = date('Y-m-d H:i:s');

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                
                                $estado = $row['Estado'];
                                $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario'];
                                $fecha_fin = $row['Fecha_Fin'];

                                // Determinar estado si no está definido
                                if (empty($estado)) {
                                    $estado = ($fecha_inicio > $hoy) ? 'Nueva' :
                                            ((empty($fecha_fin) || $fecha_fin > $hoy) ? 'Activa' : 'Finalizada');
                                }
                                
                                // Si es usuario normal y el estado calculado no es Activa/Nueva, saltar esta partida
                                if (!$esAdminOMod && !in_array($estado, ['Activa', 'Nueva'])) {
                                    continue;
                                }

                                // Formatear fechas y horas
                                $fecha_inicio_fmt = !empty($row['Fecha_Inicio'])
                                    ? date("d/m/Y", strtotime($row['Fecha_Inicio']))
                                    : '';

                                $fecha_fin_fmt = !empty($row['Fecha_Fin'])
                                    ? date("d/m/Y H:i", strtotime($row['Fecha_Fin']))
                                    : '--/--/---- --:--';

                                $horario_fmt = !empty($row['Horario'])
                                    ? date("H:i", strtotime($row['Horario']))
                                    : '';
                                
                                // Agregar clase CSS según el estado para el filtro dinámico
                                $claseEstado = 'estado-' . strtolower(str_replace(' ', '-', $estado));

                                echo "<tr class='partida-row {$claseEstado}' data-estado='{$estado}'>
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
                        } else {
                            echo "<tr><td colspan='13'>No hay partidas registradas.</td></tr>";
                        }

                        $db->cerrar();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
    
    <?php if ($esAdminOMod): ?>
        <!-- Cargar JavaScript de filtros solo para Admin/Mod -->
        <script src="assets/js/ver_partida_filtros.js"></script>
    <?php endif; ?>
</body>
</html>