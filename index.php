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

<body> <!-- Cuerpo del documento -->
    <?php include 'src/includes/header.php'; ?> <!-- Incluir el encabezado desde un archivo externo -->

    <main> <!-- Contenido principal de la página -->
        <div>
            <h1>Gestor de Partidas</h1> <!-- Título principal de la página -->
        </div>
        
        <section id="seccion-principal"> <!-- Sección principal -->
            <p class="p-texto">Bienvenido al gestor de partidas donde podras administrar las partidas de rol de la cafeteria Tollan Le Funk.</p> <!-- Texto de bienvenida -->
            
            <div id="panel-control"> <!-- Botones del panel de control -->
                <a id="btn-fondo-ver" class="btn-fondo" href="ver_partida.php" aria-label="Ver Partidas"> <!-- Enlace para ver partidas -->
                    <div class="btn-gestion">
                        <i class="fas fa-list"></i> Ver Partidas
                    </div>
                </a>

                <a id="btn-fondo-crear" class="btn-fondo" href="crear_partida.php" aria-label="Crear Partida"> <!-- Enlace para crear una nueva partida -->
                    <div class="btn-gestion">
                        <i class="fas fa-plus"></i> Crear Partida
                    </div>
                </a>

                <a id="btn-fondo-editar" class="btn-fondo" href="editar_partida.php" aria-label="Editar Partidas"> <!-- Enlace para editar partidas -->
                    <div class="btn-gestion">
                        <i class="fas fa-edit"></i> Editar Partidas
                    </div>
                </a>
            </div>
        </section>

        <section id="seccion-nosotros"> <!-- Sección "Sobre Nosotros" -->
            <div> <!-- Misión -->
                <h2 id="h-mision" class="subt-class">Misión</h2>
                <p class="p-texto">Brindar una experiencia única a los amantes de los juegos de rol, ofreciendo un espacio acogedor para disfrutar bebidas de calidad y un servicio de All-Day-Brunch, mientras fomentamos la diversión, la creatividad y la comunidad a través de partidas de rol en vivo.</p>
            </div>

            <div> <!-- Visión -->
                <h2 id="h-vision" class="subt-class">Visión</h2>
                <p class="p-texto">Ser la cafetería temática de juegos de rol líder en la región, reconocida por su ambiente innovador y amigable, donde jugadores de todas las edades se reúnan para compartir aventuras, crear historias y disfrutar de momentos memorables.</p>
            </div>
        </section>
    </main>

    <?php include 'src/includes/footer.php'; ?> <!-- Incluir el pie de página desde un archivo externo -->
</body>
</html>