<!DOCTYPE html> <!-- HTML5 -->
<html lang="es"> <!-- HTML en español -->
    <head> <!-- Metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores -->
    <meta name="description" content="Página web de gestión de juegos de rol de la cafetería Tollan le Funk"> <!-- Descripción -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuración de vista para dispositivos móviles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css"> <!-- Estilos globales -->
    <link rel="stylesheet" href="assets/css/style_index.css"> <!-- Estilos específicos para la página de inicio -->
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css"> <!-- Estilos para elementos incluidos -->
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico"> <!-- Icono de la pestaña -->
    <title>Tollan le Funk - Gestión</title> <!-- Titulo de la página -->
</head>
<!-- PAGINA PARA EL INICIO DEL PROYECTO, GENERALMENTE SIENDO EL ENLACE PREDETERMINADO PARA EL LOGIN Y REGISTRO -->
<body> <!-- Cuerpo del documento -->
    <?php
    session_start(); // Iniciar sesión para verificar estado de autenticación
    include 'src/includes/header.php'; // Incluir el encabezado desde un archivo externo
    ?>

    <main> <!-- Contenido principal de la página -->
        <div>
            <h1>Gestor de Partidas</h1> <!-- Título principal de la página -->
        </div>
        
        <section id="seccion-principal"> <!-- Sección principal del gestor de partidas -->
            <h2 class="subt-class">Panel de control</h2>
            <p class="p-texto">Bienvenido al gestor de partidas donde podrás administrar las partidas de rol de la cafetería Tollan Le Funk.</p>
            
            <div id="panel-control">
                <!-- Ver Partidas: Disponible para todos (requiere login) -->
                <a id="btn-fondo-ver" class="btn-fondo" 
                   href="<?php echo isset($_SESSION['ID_Usuarios']) ? 'ver_partida.php' : 'src/login.php?redirect=' . urlencode('/Proyecto_Tollan_Le_Funk/ver_partida.php'); ?>" 
                   aria-label="Ver Partidas">
                    <div class="btn-gestion">
                        <i class="fas fa-list"></i> Ver Partidas
                    </div>
                </a>

                <!-- Crear Partida: Solo Adm y Mod -->
                <a id="btn-fondo-crear" class="btn-fondo" 
                   href="<?php 
                       if (!isset($_SESSION['ID_Usuarios'])) {
                           echo 'src/login.php?redirect=' . urlencode('/Proyecto_Tollan_Le_Funk/crear_partida.php');
                       } elseif ($_SESSION['Tipo_Usr'] === 'Adm' || $_SESSION['Tipo_Usr'] === 'Mod') {
                           echo 'crear_partida.php';
                       } else {
                           echo 'ver_partida.php';
                       }
                   ?>" 
                   aria-label="Crear Partida">
                    <div class="btn-gestion">
                        <i class="fas fa-plus"></i> Crear Partida
                        <?php if (isset($_SESSION['ID_Usuarios']) && $_SESSION['Tipo_Usr'] === 'Usr'): ?>
                            <small style="display:block; font-size:0.8em;">(Requiere permisos)</small>
                        <?php endif; ?>
                    </div>
                </a>

                <!-- Editar Partidas: Solo Adm y Mod -->
                <a id="btn-fondo-editar" class="btn-fondo" 
                   href="<?php 
                       if (!isset($_SESSION['ID_Usuarios'])) {
                           echo 'src/login.php?redirect=' . urlencode('/Proyecto_Tollan_Le_Funk/editar_partida.php');
                       } elseif ($_SESSION['Tipo_Usr'] === 'Adm' || $_SESSION['Tipo_Usr'] === 'Mod') {
                           echo 'editar_partida.php';
                       } else {
                           echo 'ver_partida.php';
                       }
                   ?>"
                   
                   aria-label="Editar Partidas">
                    <div class="btn-gestion">
                        <i class="fas fa-edit"></i> Editar Partidas
                        <?php if (isset($_SESSION['ID_Usuarios']) && $_SESSION['Tipo_Usr'] === 'Usr'): ?>
                            <small style="display:block; font-size:0.8em;">(Requiere permisos)</small>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </section>

        <section id="seccion-nosotros"> <!-- Sección sobre nosotros -->
            <div>
                <h2 id="h-mision" class="subt-class">Misión</h2>
                <p class="p-texto">Brindar una experiencia única a los amantes de los juegos de rol, ofreciendo un espacio acogedor para disfrutar bebidas de calidad y un servicio de All-Day-Brunch, mientras fomentamos la diversión, la creatividad y la comunidad a través de partidas de rol en vivo.</p>
            </div>

            <div>
                <h2 id="h-vision" class="subt-class">Visión</h2>
                <p class="p-texto">Ser la cafetería temática de juegos de rol líder en la región, reconocida por su ambiente innovador y amigable, donde jugadores de todas las edades se reúnan para compartir aventuras, crear historias y disfrutar de momentos memorables.</p>
            </div>
        </section>
    </main>

    <?php include 'src/includes/footer.php'; ?> <!-- Incluir el pie de página desde un archivo externo -->
</body>
</html>
<!-- GENERAR DOCUMENTACION AL INICIO DEL PROYECTO, PARA PROXIMAS CONSULTAS  -->