<?php
include 'check_auth.php';
verificarAutenticacion();

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de edición del usuario de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style_registro.css'); ?>">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style_user.css'); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo url('assets/img/dragon.ico'); ?>">
    <title>Tollan le Funk - Perfil</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div>
            <h1>Perfil de Usuario</h1>
        </div>
        
        <section id="seccion-principal">
            <h2 class="subt-class">Información del Usuario</h2>
            <div class="card-user">
                <p><i class="fas fa-user"></i> <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['Nombre']); ?></p>
                <p><i class="fas fa-envelope"></i> <strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['Correo']); ?></p>
                <p><i class="fas fa-user-tag"></i> <strong>Rol:</strong> 
                    <?php 
                        $rol = $_SESSION['Tipo_Usr'];
                        $rolNombre = $rol === 'Adm' ? 'Administrador' : ($rol === 'Mod' ? 'Moderador' : 'Usuario');
                        echo htmlspecialchars($rolNombre); 
                    ?>
                </p>
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

            <div class="div-form div-cambio">
                <h3>Datos Personales</h3>
                <form id="form-editar-usuario" action="procesar_edicion_usuario.php" method="POST">
                    <label for="nombre"><i class="fas fa-user-edit"></i> Nombre de Usuario:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['Nombre']); ?>" required>
                    <span id="mensaje-nombre"></span>

                    <label for="correo"><i class="fas fa-envelope"></i> Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_SESSION['Correo']); ?>" required>
                    <span id="mensaje-correo"></span>

                    <button id="btn-guardar-cambios" class="btn btn-actualizar" type="submit">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </form>
            </div>
        </section>

        <section id="seccion-danger">
            <h2 class="subt-class">Opciones Peligrosas</h2>
            
            <div class="div-form div-cambio">
                <h3>Cambiar Contraseña</h3>
                <form id="form-cambiar-password" action="procesar_cambio_password.php" method="POST">
                    <label for="password_actual"><i class="fas fa-lock"></i> Contraseña Actual:</label>
                    <input type="password" id="password_actual" name="password_actual" required>

                    <label for="nueva_password"><i class="fas fa-key"></i> Nueva Contraseña:</label>
                    <input type="password" id="nueva_password" name="nueva_password" required minlength="6">
                    <span id="mensaje-nueva-pass"></span>

                    <label for="confirmar_password"><i class="fas fa-key"></i> Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required>
                    <span id="mensaje-confirmar-pass"></span>

                    <button id="btn-cambiar-password" class="btn btn-actualizar" type="submit">
                        <i class="fas fa-sync-alt"></i> Cambiar Contraseña
                    </button>
                </form>
            </div>

            <?php if ($_SESSION['Tipo_Usr'] === 'Usr'): ?>
                <div class="div-form div-delete">
                    <h3>Eliminar Cuenta</h3>
                    <p style="color: #ff4d4d; font-weight: bold;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta acción es IRREVERSIBLE. Perderás todos tus datos permanentemente.
                    </p>
                    <form id="form-eliminar-usuario" action="procesar_eliminacion_usuario.php" method="POST">
                        <label for="confirmar_delete"><i class="fas fa-exclamation-circle"></i> Escribe "ELIMINAR" para confirmar:</label>
                        <input type="text" id="confirmar_delete" name="confirmar_delete" required placeholder="ELIMINAR">
                        <span id="mensaje-confirmar-delete"></span>

                        <label for="confirmar_eliminacion"><i class="fas fa-lock"></i> Tu Contraseña:</label>
                        <input type="password" id="confirmar_eliminacion" name="confirmar_eliminacion" required>

                        <button id="btn-eliminar-cuenta" class="btn btn-borrar" type="submit">
                            <i class="fas fa-trash-alt"></i> Eliminar Cuenta Permanentemente
                        </button>
                    </form>
                </div>
            <?php elseif ($_SESSION['Tipo_Usr'] === 'Mod'): ?>
                <div class="div-form div-delete">
                    <h3>Eliminar Cuenta</h3>
                    <p style="color: #ff8c00; font-weight: bold;">
                        <i class="fas fa-info-circle"></i>
                        Para eliminar una cuenta de Moderador es necesario contactar con soporte.
                    </p>
                    <p style="text-align: center; margin-top: 15px;">
                        <a href="mailto:soporte@tollanlefunk.com" style="color: #4d94ff; text-decoration: underline; font-size: 1.1rem;">
                            <i class="fas fa-envelope"></i> soporte@tollanlefunk.com
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>
    <script src="<?php echo url('assets/js/user.js'); ?>"></script>
</body>
</html>