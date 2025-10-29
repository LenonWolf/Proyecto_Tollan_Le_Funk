<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de registro de usuarios de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="../assets/img/dragon.ico">
    <title>Tollan le Funk - Registro</title>
    
    <style>
        /* Estilos para mensajes de validación */
        .mensaje-error {
            color: #d32f2f;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .mensaje-exito {
            color: #388e3c;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .mensaje-info {
            color: #1976d2;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        .input-error {
            border-color: #d32f2f !important;
        }
        
        .input-exito {
            border-color: #388e3c !important;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php
    include 'conexion.php';
    include 'includes/header.php';
    ?>
    
    <main>
        <h1>Registro de Usuario</h1>
        <form id="form-registro" action="procesar_registro.php" method="POST">

            <div class="div-form">
                <h2>Información del Usuario</h2>

                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" required>
                <span id="mensaje-username" class="mensaje-error"></span>

                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
                <span id="mensaje-email"></span>
            
                <h2>Contraseña</h2>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required minlength="6">
                <span id="mensaje-password" class="mensaje-error"></span>

                <label for="confirm_password">Confirmar contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span id="mensaje-confirm" class="mensaje-error"></span>
            </div>

            <button id="btn-registrar" class="btn" type="submit">Registrar</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Vue.js 3 CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>
    
    <!-- Script de registro con validaciones -->
    <script src="/Tollan_Le_Funk/assets/js/registro.js"></script>
</body>
</html>