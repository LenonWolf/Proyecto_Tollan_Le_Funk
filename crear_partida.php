<!DOCTYPE html> <!-- HTML5 -->
<html lang="es"> <!-- HTML en español -->
<head> <!-- Metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores -->
    <meta name="description" content="Página web de creación de juegos de rol de la cafetería Tollan le Funk"> <!-- Descripción -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración de vista para dispositivos móviles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css"> <!-- Estilos globales -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css"> <!-- Estilos del formulario -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css"> <!-- Estilos de botones -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos para elementos incluidos -->
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico"> <!-- Icono de la pestaña -->
    <title>Tollan le Funk - Creación</title> <!-- Titulo de la página -->
</head>

<body> <!-- Cuerpo del documento -->
    <?php
    // PROTECCIÓN: Verificar autenticación y permisos
    include 'src/check_auth.php';
    verificarPermisos(['Adm', 'Mod']); // Solo administradores y moderadores
    
    // Continuar con el código normal
    include 'src/conexion.php';
    include 'src/includes/header.php';

    // Listas
    $sql_sistemas = "SELECT ID_Sistema, Titulo FROM sistema ORDER BY Titulo"; // Consulta para obtener los sistemas de juego
    $sistemas_result = $conn->query($sql_sistemas); // Ejecutar la consulta y obtener resultados

    $sql_dms = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre"; // Consulta para obtener los directores de juego
    $dms_result = $conn->query($sql_dms); // Ejecutar la consulta y obtener resultados

    // Restricciones
    date_default_timezone_set('America/Mexico_City'); // Establecer la zona horaria
    $hoy = date('Y-m-d'); // Fecha actual

    // Configuración para el formulario
    $action = "src/submit_partida.php"; // Archivo que procesará el formulario
    $partida = []; // Partida vacía

    ?>
    <main> <!-- Contenido principal de la página -->
        <h1>Creador de Partida</h1> <!-- Título principal de la página -->
        
        <?php include 'src/includes/form_partida.php'; ?> <!-- Incluir el formulario de creación de partida desde un archivo externo -->
        <button id="btn-crear" class="btn" form="form-partida" type="submit">Crear Partida</button> <!-- Botón para enviar el formulario -->
    </main>

    <?php include 'src/includes/footer.php'; ?> <!-- Incluir el pie de página desde un archivo externo -->
</body>
</html>