<?php
include 'src/conexion.php';

// === Consulta SQL ===
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
ORDER BY partida.ID_Partida ASC";

$result = $conn->query($sql);
$partidas = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // Cálculo automático del estado (si no está definido)
    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d H:i:s');
    $fecha_inicio = $row['Fecha_Inicio'] . ' ' . $row['Horario'];
    $fecha_fin = $row['Fecha_Fin'];

    if (empty($row['Estado'])) {
      if ($fecha_inicio > $hoy) {
        $row['Estado'] = 'Nueva';
      } elseif (empty($fecha_fin) || $fecha_fin > $hoy) {
        $row['Estado'] = 'Activa';
      } else {
        $row['Estado'] = 'Finalizada';
      }
    }

    // Formateo de fechas
    $row['Fecha_Inicio'] = !empty($row['Fecha_Inicio'])
      ? date("d/m/Y", strtotime($row['Fecha_Inicio'])) : '--/--/----';

    $row['Fecha_Fin'] = !empty($row['Fecha_Fin'])
      ? date("d/m/Y", strtotime($row['Fecha_Fin'])) : '--/--/----';

    $row['Horario'] = !empty($row['Horario'])
      ? date("H:i", strtotime($row['Horario'])) : '--:--';

    $partidas[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
  <meta name="description" content="Visualizador de partidas de rol - Tollan le Funk">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tollan le Funk - Visualización</title>

  <!-- CSS externo -->
  <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
  <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
  <link rel="icon" type="image/x-icon" href="../assets/img/dragon.ico">
</head>
<body>
  <?php include 'src/includes/header.php'; ?>

  <div id="app">
    <main>
      <h1>Visualizador de Partidas</h1>

      <div v-if="loading" class="loading">Cargando partidas...</div>
      <div v-else-if="error" class="error">{{ error }}</div>

      <div v-else class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
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
            <tr v-for="(p, index) in partidas" :key="index">
              <td>{{ p.ID_Partida }}</td>
              <td>{{ p.Titulo }}</td>
              <td>{{ p.Sistema_Titulo }}</td>
              <td>{{ p.Sistema_Descripcion }}</td>
              <td>{{ p.Fecha_Inicio }}</td>
              <td>{{ p.Fecha_Fin }}</td>
              <td>{{ p.Horario }}</td>
              <td>{{ p.Periocidad }}</td>
              <td>{{ p.DM_Nombre }}</td>
              <td>{{ p.Numero_Jugadores }}</td>
              <td>{{ p.Clasificacion }}</td>
              <td>{{ p.Tipo }}</td>
              <td>{{ p.Estado }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <?php include 'src/includes/footer.php'; ?>

  <!-- Vue JS -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

  <script>
    const { createApp } = Vue;

    createApp({
      data() {
        return {
          partidas: <?php echo json_encode($partidas, JSON_UNESCAPED_UNICODE); ?>,
          loading: false,
          error: null
        };
      }
    }).mount("#app");
  </script>
</body>
</html>
