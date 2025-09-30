<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Luis Eduardo Nieves Avila y Juan Alberto Sanchez Hernandez">
    <meta name="description" content="Página web de modificación de juegos de rol de la cafetería Tollan le Funk">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/global.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_form.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/style_btn.css">
    <link rel="stylesheet" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/css/includes.css">
    <link rel="icon" type="image/x-icon" href="https://lenonwolf.github.io/Assets_Tollan_Le_Funk/img/dragon.ico">
    <title>Tollan le Funk - Modificación</title>
</head>

<body>
    <?php
    include 'conexion.php';

    // Cargar partida
    $id = $_GET['id'] ?? null;
    if (!$id) { die("ID inválido"); }
    $res = $conn->query("SELECT * FROM partida WHERE ID_Partida = $id");
    $partida = $res->fetch_assoc();

    // Listas
    $sql_sistemas = "SELECT ID_Sistema, Titulo FROM sistema ORDER BY Titulo";
    $sistemas_result = $conn->query($sql_sistemas);

    $sql_dms = "SELECT ID_DM, Nombre FROM dm ORDER BY Nombre";
    $dms_result = $conn->query($sql_dms);

    // Restricciones
    date_default_timezone_set('America/Mexico_City');
    $hoy = date('Y-m-d');

    // Config para el form
    $action = "update_partida.php?id=$id";
    $boton_text = "Actualizar";
    ?>
    <main>
        <h1>Modificador de Partida</h1>
        <p id="nombre-partida"><?php echo htmlspecialchars($partida['Titulo']); ?></p>

        <?php include 'includes/form_partida.php'; ?>
        <form id="form-borrar" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $partida['ID_Partida']; ?>">
        </form>

        <div id="div-acciones" class="acciones">
            <button id="btn-actualizar" class="btn" form="form-partida" type="submit">Actualizar</button>
            <button id="btn-cancelar" class="btn" type="button" onclick="window.close();">Cancelar</button>
            <button id="btn-borrar" class="btn" form="form-borrar" type="submit">Borrar</button>
        </div>

        <div id="div-acciones-2" class="acciones">
            <button id="btn-actualizar-2" class="btn-2" form="form-partida" type="submit"><i class="fas fa-save"></i></button>
            <button id="btn-cancelar-2" class="btn-2" type="button" onclick="window.close();"><i class="fas fa-times"></i></button>
            <button id="btn-borrar-2" class="btn-2" form="form-borrar" type="submit"><i class="fas fa-trash"></i></button>
        </div>

        <script>
        document.getElementById('form-borrar').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!confirm("¿Seguro que deseas eliminar esta partida? Esta acción no se puede deshacer.")) {
                return;
            }

            const formData = new FormData(this);

            try {
                const resp = await fetch('delete_partida.php', {
                    method: 'POST',
                    body: formData
                });

                if (resp.ok) {
                    // Avisar al padre que refresque (si existe)
                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    }
                    // Cerrar la ventana emergente
                    window.close();
                } else {
                    alert("Error al eliminar la partida.");
                }
            } catch (err) {
                console.error(err);
                alert("Error de red al eliminar la partida.");
            }
        });
        </script>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>