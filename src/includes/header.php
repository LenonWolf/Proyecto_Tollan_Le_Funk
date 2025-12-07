<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Encabezado con el título del sitio y el menú de navegación -->
<header>
    <p id="p-encabezado">Tollan Le Funk</p> <!-- Encabezado de la pagina -->
    <input type="checkbox" id="btn-menu"> <!-- Verificación para ocultar el menú en la versión movil -->
    <label for="btn-menu" id="lbl-menu" class="lbl-menu-c"> <!-- Boton para abrir el menú en la versión movil -->
        <i class="fas fa-bars"></i>
    </label>

    <label for="btn-menu" id="lbl-cmenu" class="lbl-cmenu-c"> <!-- Boton para cerrar el menú en la versión movil -->
        <i class="fas fa-times"></i>
    </label>

    <nav id="nav-menu" aria-label="Menú principal"> <!-- Menú de navegación -->
        <ul>
            <?php if (isset($_SESSION['Tipo_Usr'])): ?>
                <!-- Usuario autenticado -->
                
                <?php if ($_SESSION['Tipo_Usr'] === 'Adm' || $_SESSION['Tipo_Usr'] === 'Mod'): ?>
                    <!-- Administrador o Moderador: acceso completo -->
                    <li><a class="a-menus" href="/index.php" aria-label="Inicio">Inicio</a></li>
                    <li><a class="a-menus" href="/ver_partida.php" aria-label="Ver Partidas">Ver</a></li>
                    <li><a class="a-menus" href="/crear_partida.php" aria-label="Crear Partida">Crear</a></li>
                    <li><a class="a-menus" href="/editar_partida.php" aria-label="Editar Partidas">Editar</a></li>
                    
                <?php else: ?>
                    <!-- Usuario normal: solo ver partidas -->
                    <li><a class="a-menus" href="/index.php" aria-label="Inicio">Inicio</a></li>
                    <li><a class="a-menus" href="/ver_partida.php" aria-label="Ver Partidas">Ver</a></li>
                <?php endif; ?>
                
                <!-- Menú desplegable del usuario -->
                <li class="dropdown-user">
                    <a class="a-menus" href="#" aria-label="Menú de usuario">
                        <?php echo htmlspecialchars($_SESSION['Nombre']); ?>
                        <i class="fas fa-chevron-down icon-down"></i>
                        <i class="fas fa-chevron-up icon-up"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/src/user.php"><i class="fas fa-user-edit"></i> Perfil</a></li>
                        <li><a href="/src/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
                    </ul>
                </li>
                
            <?php else: ?>
                <!-- Usuario no autenticado -->
                <li><a class="a-menus" href="/index.php" aria-label="Inicio">Inicio</a></li>
                <li><a class="a-menus" href="/src/login.php" aria-label="Iniciar sesión">
                    Login
                </a></li>
                <li><a class="a-menus" href="/src/registro.php" aria-label="Registrarse">
                    Registro
                </a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script src="/assets/js/header_dinamic.js"></script>
