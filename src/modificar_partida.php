<?php
include 'src/check_auth.php';
verificarPermisos(['Adm', 'Mod']);

require_once 'src/conexion.php';

$db = new Conexion('usr_lector', 'lector123');
$conn = $db->conectar();
?>
<!DOCTYPE html> <!-- Declaración de tipo de documento: HTML5 -->
<html lang="es"> <!-- Documento en idioma español -->
<head> <!-- Cabecera: metadatos y enlaces a recursos externos -->
    <meta charset="UTF-8"> <!-- Codificación de caracteres en UTF-8 -->
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez"> <!-- Autores del documento -->
    <meta name="description" content="Página web de modificación de juegos de rol de la cafetería Tollan le Funk"> <!-- Descripción para buscadores -->
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
    
    // Obtener el ID de la partida desde la URL (GET).
    // Si no existe, se detiene la ejecución con un mensaje de error.
    $id = $_GET['id'] ?? null;
    if (!$id) { die("ID inválido"); }

    $res = $conn->query("SELECT * FROM partida WHERE ID_Partida = $id"); // Consultar los datos de la partida a modificar
    $partida = $res->fetch_assoc(); // Guardar los datos en un arreglo asociativo

    // Consultar todos los sistemas disponibles (para llenar el <select>)
    $sql_sistemas = "SELECT ID_Sistema, Titulo FROM sistema ORDER BY Titulo";
    $sistemas_result = $conn->query($sql_sistemas);

    // Consultar todos los Dungeon Masters disponibles (para llenar el <select>)
    $sql_dms = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre";
    $dms_result = $conn->query($sql_dms);

    // Definir zona horaria y obtener la fecha actual (para restricciones en el formulario)
    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d');

    // Definir la acción del formulario (archivo que procesará la actualización)
    $action = "update_partida.php?id=$id";
    $boton_text = "Actualizar"; // Texto del botón principal
    ?>
    
    <main> <!-- Contenido principal de la página -->
        <h1>Modificador de Partida</h1> <!-- Encabezado principal -->
        
        <!-- Mostrar el título actual de la partida (escapado con htmlspecialchars para seguridad XSS) -->
        <p id="nombre-partida"><?php echo htmlspecialchars($partida['Titulo']); ?></p>

        <!-- Incluir el formulario reutilizable de partida -->
        <?php include 'includes/form_partida.php'; ?>

        <!-- Formulario oculto para borrar la partida -->
        <form id="form-borrar" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $partida['ID_Partida']; ?>">
        </form>

        <!-- Grupo de botones de acción (versión con texto) -->
        <div id="div-acciones" class="acciones">
            <button id="btn-actualizar" class="btn" form="form-partida" type="submit">Actualizar</button>
            <button id="btn-cancelar" class="btn" type="button" onclick="window.close();">Cancelar</button>
            <button id="btn-borrar" class="btn" form="form-borrar" type="submit">Borrar</button>
        </div>

        <!-- Grupo de botones de acción (versión con iconos Font Awesome) -->
        <div id="div-acciones-2" class="acciones">
            <button id="btn-actualizar-2" class="btn-2" form="form-partida" type="submit"><i class="fas fa-save"></i></button>
            <button id="btn-cancelar-2" class="btn-2" type="button" onclick="window.close();"><i class="fas fa-times"></i></button>
            <button id="btn-borrar-2" class="btn-2" form="form-borrar" type="submit"><i class="fas fa-trash"></i></button>
        </div>

        <!-- Script JS asociado para manejar interacciones dinámicas -->
        <script src="../assets/js/modificar_partida.js"></script>
    </main>

    <?php include 'includes/footer.php'; ?> <!-- Incluir el pie de página común -->
</body>
</html>