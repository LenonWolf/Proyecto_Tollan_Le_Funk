<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de creación de juegos de rol de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/img/dragon.ico">
    <title>Tollan le Funk - Creación</title>
</head>

<body>
    <?php
    include 'src/conexion.php';
    include 'src/includes/header.php';

    // Listas
    $sql_sistemas = "SELECT ID_Sistema, Titulo FROM sistema ORDER BY Titulo";
    $sistemas_result = $conn->query($sql_sistemas);

    $sql_dms = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre";
    $dms_result = $conn->query($sql_dms);

    // Restricciones
    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d');

    // Config para el form
    $action = "src/submit_partida.php";
    $partida = []; // vacío

    ?>
    <main>
        <h1>Creador de Partida</h1>
        
        <?php include 'src/includes/form_partida.php'; ?>
        <button id="btn-crear" class="btn" form="form-partida" type="submit">Crear Partida</button>
    </main>
    <?php include 'src/includes/footer.php'; ?>
</body>
</html>