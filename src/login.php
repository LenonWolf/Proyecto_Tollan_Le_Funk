<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de login de usuarios de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="../assets/img/dragon.ico">
    <title>Tollan le Funk - Login</title>
</head>

<body>
    <?php
    include 'conexion.php';
    include 'includes/header.php';
    ?>
    
    <main>
        <h1>Login de Usuario</h1>
        <form id="form-login" action="procesar_login.php" method="POST">

            <div class="div-form">
                <h2>Ingresa tus credenciales</h2>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
            
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button id="btn-login" class="btn" type="submit">Iniciar Sesión</button>
            
            <p style="text-align: center; margin-top: 15px;">
                ¿No tienes cuenta? <a href="registro.php" style="color: #4CAF50;">Regístrate aquí</a>
            </p>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Vue.js 3 CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>
    
    <!-- Script de login con AJAX -->
    <script src="/Tollan_Le_Funk/assets/js/login.js"></script>
</body>
</html>