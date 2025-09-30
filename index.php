<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de gestión de juegos de rol de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/style_index.css">
    <link rel="stylesheet" href="assets/css/includes.css">
    <link rel="icon" type="image/x-icon" href="assets/img/dragon.ico">
    <title>Tollan le Funk - Gestión</title>
</head>

<body>
    <?php include 'src/includes/header.php'; ?>

    <main>
        <div>
            <h1>Gestor de Partidas</h1>
            <p>Bienvenido al gestor de partidas donde podras administrar las partidas de rol de la cafeteria Tollan Le Funk.</p>
            
            <!-- Botones del panel de control -->
            <div id="panel-control">
                <a id="btn-fondo-ver" class="btn-fondo" href="ver_partida.php" aria-label="Ver Partidas">
                    <div class="btn-gestion">
                        <i class="fas fa-list"></i> Ver Partidas
                    </div>
                </a>

                <a id="btn-fondo-crear" class="btn-fondo" href="crear_partida.php" aria-label="Crear Partida">
                    <div class="btn-gestion">
                        <i class="fas fa-plus"></i> Crear Partida
                    </div>
                </a>

                <a id="btn-fondo-editar" class="btn-fondo" href="editar_partida.php" aria-label="Editar Partidas">
                    <div class="btn-gestion">
                        <i class="fas fa-edit"></i> Editar Partidas
                    </div>
                </a>
            </div>
        </div>

        <div>
            <h2 id="h-mision" class="subt-class">Misión</h2>
            <p>Brindar una experiencia única a los amantes de los juegos de rol, ofreciendo un espacio acogedor para disfrutar bebidas de calidad y un servicio de All-Day-Brunch, mientras fomentamos la diversión, la creatividad y la comunidad a través de partidas de rol en vivo.</p>
        </div>

        <div>
            <h2 id="h-vision" class="subt-class">Visión</h2>
            <p>Ser la cafetería temática de juegos de rol líder en la región, reconocida por su ambiente innovador y amigable, donde jugadores de todas las edades se reúnan para compartir aventuras, crear historias y disfrutar de momentos memorables.</p>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
</body>
</html>