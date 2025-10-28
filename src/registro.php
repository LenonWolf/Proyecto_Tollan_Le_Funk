<!DOCTYPE html> <!-- Declaración de tipo de documento: HTML5 -->
<html lang="es"> <!-- Documento en idioma español -->
<head> <!-- Cabecera: metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres en UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores del documento -->
    <meta name="description" content="Página web de registro de usuarios de la cafetería Tollan le Funk"> <!-- Descripción para buscadores -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Adaptación a dispositivos móviles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Librería de iconos Font Awesome -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css"> <!-- Estilos globales -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css"> <!-- Estilos específicos para formularios -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css"> <!-- Estilos de botones -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos para elementos incluidos -->
    <link rel="icon" type="image/x-icon" href="../assets/img/dragon.ico"> <!-- Icono de la pestaña del navegador -->
    <title>Tollan le Funk - Modificación</title> <!-- Título de la página -->
</head>

<body> <!-- Cuerpo del documento -->
    <?php
    include 'conexion.php'; // Incluir la conexión a la base de datos
    include 'includes/header.php'; // Incluir el encabezado desde un archivo externo
    ?>
    
    <main> <!-- Contenido principal de la página -->
        <h1>Registro de Usuario</h1>
        <form action="procesar_registro.php" method="POST"> <!-- Formulario de registro -->
            <h2>Información del Usuario</h2>

            <div class="div-form">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <h2>Contraseña</h2>

            <div class="div-form">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirmar contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button id="btn-registrar" class="btn" type="submit">Registrar</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?> <!-- Incluir el pie de página común -->
</body>
</html>