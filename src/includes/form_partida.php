<?php
$titulo = $partida['Titulo'] ?? '';
$fecha_inic = $partida['Fecha_Inic'] ?? '';
$horario = $partida['Horario'] ?? '';
$periocidad = $partida['Periocidad'] ?? '';
$no_jugadores = $partida['No_Jugadores'] ?? '';
$id_sistema = $partida['ID_Sistema'] ?? '';
$id_dm = $partida['ID_DM'] ?? '';
?>

<form id="form-partida" action="<?php echo $action; ?>" method="post">
    <?php if (!empty($partida['ID_Partida'])): ?>
        <input type="hidden" name="ID_Partida" value="<?php echo $partida['ID_Partida']; ?>">
    <?php endif; ?>

    <!-- SISTEMA -->
    <h2>Datos del Sistema</h2>
    <label for="select-sistema"><strong>Sistema:</strong></label>
    <select id="select-sistema" name="ID_Sistema" required>
        <option value="">--- Selecciona un sistema ---</option>
        <?php while($row = $sistemas_result->fetch_assoc()): ?>
            <option value="<?php echo $row['ID_Sistema']; ?>"
                <?php echo ($row['ID_Sistema'] == $id_sistema) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($row['Titulo']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <div id="div-infoSistema">
        <p><strong>Descripción:</strong> <span id="s_descripcion">--------------------</span></p>
        <p><strong>Clasificación:</strong> <span id="s_clasificacion">--------------------</span></p>
        <p><strong>Tipo:</strong> <span id="s_tipo">--------------------</span></p>
        <p><strong>Género:</strong> <span id="s_genero">--------------------</span></p>
        <p><strong>Dados:</strong> <span id="s_dado">--------------------</span></p>
    </div>

    

    <!-- DM -->
    <h2>Dungeon Master</h2>
    <select id="select-dm" name="ID_DM" required>
        <option value="">--- Selecciona un DM ---</option>
        <?php while($row = $dms_result->fetch_assoc()): ?>
            <option value="<?php echo $row['ID_DM']; ?>"
                <?php echo ($row['ID_DM'] == $id_dm) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($row['Nombre']); ?>
            </option>
        <?php endwhile; ?>
        <option value="new">Agregar nuevo DM +</option>
    </select>

    <div id="div-nuevoDm" class="div-form" style="display:none;">
        <h3>Nuevo DM</h3>
        
        <label for="lbl-nombre">Nombre:</label>
        <input type="text" id="lbl-nombre" name="lbl-nombre">

        <label for="lbl-fecha-nac">Fecha de nacimiento:</label>
        <input type="date" id="lbl-fecha-nac" name="lbl-fecha-nac">

        <p class="notas"><strong>Nota:</strong> Si el DM ya existe, no se creará otro.</p>
    </div>

    

    <!-- DATOS PARTIDA -->
    <h2>Datos de la partida</h2>
    
    <div class="div-form">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="Titulo" value="<?php echo $titulo; ?>" required>

        <label for="fecha_inic">Fecha inicio:</label>
        <input type="date" id="fecha_inic" name="Fecha_Inic" value="<?php echo $fecha_inic; ?>"
            <?php if (empty($partida['ID_Partida'])): ?> 
                min="<?php echo $hoy; ?>"
            <?php endif; ?>
            required>


        <label for="horario">Horario:</label>
        <input type="time" id="horario" name="Horario" value="<?php echo $horario; ?>" min="11:00" max="19:00" required>

        <label for="periocidad">Periocidad:</label>
        <select id="periocidad" name="Periocidad" required>
            <option value="" disabled <?php echo empty($periocidad) ? 'selected' : ''; ?>>--- Selecciona la periocidad ---</option>
            <option value="Semanal" <?php echo ($periocidad=="Semanal" ? "selected" : ""); ?>>Semanal</option>
            <option value="Quincenal" <?php echo ($periocidad=="Quincenal" ? "selected" : ""); ?>>Quincenal</option>
            <option value="One_Shot" <?php echo ($periocidad=="One_Shot" ? "selected" : ""); ?>>One_Shot</option>
        </select>


        <label for="no_jugadores">Número de jugadores:</label>
        <input type="number" id="no_jugadores" name="No_Jugadores" value="<?php echo $no_jugadores; ?>" min="1" required>

        <p class="notas">
            <strong>Nota:</strong> El horario debe estar entre las 11:00 y las 19:00. De lunes a viernes.
        </p>
    </div>
</form>

<script src="/Tollan_Le_Funk/assets/js/form_partida.js"></script>