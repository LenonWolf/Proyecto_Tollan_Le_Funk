<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de edición del usuario de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css"> <!-- Estilos del formulario -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="../assets/img/dragon.ico">
    <title>Tollan le Funk - Perfil</title>

    <style>
        .card-user {
            background-color: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .card-user p {
            font-size: 1.2rem;
            margin: 8px 0;
        }
    </style>
</head>

<body>
    <?php 
    // Iniciar sesión para verificar estado de autenticación
    session_start();
    if (!isset($_SESSION['Tipo_Usr'])) {
        header("Location: src/login.php");
        exit;
    }

    include 'includes/header.php';
    ?>

    <main>
        <div>
            <h1>Perfil de Usuario</h1>
        </div>
        
        <section id="seccion-principal">
            <h2 class="subt-class">Información del Usuario</h2>
            <div class="card-user">
                <p><i class="fas fa-user"></i> <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['Nombre']); ?></p>
                <p><i class="fas fa-envelope"></i> <strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['Correo']); ?></p>
                <p><i class="fas fa-user-tag"></i> <strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['Tipo_Usr']); ?></p>
                <p><i class="fas fa-calendar-alt"></i> <strong>Fecha de Registro:</strong>
                    <?php 
                        echo isset($_SESSION['Fecha_Alt']) 
                            ? htmlspecialchars(date("d/m/Y", strtotime($_SESSION['Fecha_Alt']))) 
                            : "No disponible"; 
                    ?>
                </p>
            </div>
        </section>

        <section id="seccion-edicion">
            <h2 class="subt-class">Editar Información</h2>

            <div class="div-form">
                <form id="form-editar-usuario" action="procesar_edicion_usuario.php" method="POST">
                    <label for="nombre"><i class="fas fa-user-edit"></i> Nombre de Usuario:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['Nombre']); ?>" required>

                    <label for="correo"><i class="fas fa-envelope"></i> Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_SESSION['Correo']); ?>" required>

                    <button id="btn-guardar-cambios" class="btn" type="submit">Guardar Cambios</button>
                </form>
            </div>
        </section>

        <section id="seccion-danger">
            <h2 class="subt-class">Opciones Peligrosas</h2>
            
            <div class="div-form">
                <h3>Cambiar Contraseña</h3>
                <form id="form-cambiar-password" action="procesar_cambio_password.php" method="POST">
                    <label for="password_actual">Contraseña Actual:</label>
                    <input type="password" id="password_actual" name="password_actual" required>

                    <label for="nueva_password">Nueva Contraseña:</label>
                    <input type="password" id="nueva_password" name="nueva_password" required>

                    <label for="confirmar_password">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required>

                    <button id="btn-cambiar-password" class="btn" type="submit">Cambiar Contraseña</button>
                </form>
            </div>

            <div class="div-form">
                <h3>Eliminar Cuenta</h3>
                <form id="form-eliminar-usuario" action="procesar_eliminacion_usuario.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
                    <label for="confirmar_delete">Escribe "ELIMINAR" para confirmar:</label>
                    <input type="text" id="confirmar_delete" name="confirmar_delete" required>

                    <label for="confirmar_eliminacion">Contraseña:</label>
                    <input type="password" id="confirmar_eliminacion" name="confirmar_eliminacion" required>

                    <button id="btn-eliminar-cuenta" class="btn btn-danger" type="submit">Eliminar Cuenta</button>
                </form>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
<script>
document.getElementById('form-cambiar-password').addEventListener('submit', function(e) {
    const newPass = document.getElementById('nueva_password').value;
    const confirmPass = document.getElementById('confirmar_password').value;

    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Las contraseñas no coinciden.');
    }
});
</script>

</html>