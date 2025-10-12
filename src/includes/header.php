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
            <li><a class="a-menus" href="index.php" aria-label="Inicio">Inicio</a></li>
            <li><a class="a-menus" href="ver_partida.php" aria-label="Ver Partidas">Ver</a></li>
            <li><a class="a-menus" href="crear_partida.php" aria-label="Crear Partida">Crear</a></li>
            <li><a class="a-menus" href="editar_partida.php" aria-label="Editar Partidas">Editar</a></li>
        </ul>
    </nav>
</header>