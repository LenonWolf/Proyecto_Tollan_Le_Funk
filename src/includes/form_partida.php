<?php
// Inicializar variables con valores de la partida si existen
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

    <!-- Atributos data-* para Vue (W3C válido) -->
    <input type="hidden" id="data-sistema-inicial" value="<?php echo $id_sistema; ?>">
    <input type="hidden" id="data-dm-inicial" value="<?php echo $id_dm; ?>">

    <!-- SISTEMA -->
    <h2>Datos del Sistema</h2>
    
    <label for="select-sistema"><strong>Sistema:</strong></label>
    <select id="select-sistema" name="ID_Sistema" required>
        <option value="">--- Selecciona un sistema ---</option>
        <!-- Vue llenará estas opciones dinámicamente -->
    </select>

    <!-- Información dinámica del sistema (Vue la actualiza) -->
    <div id="div-infoSistema">
        <p><strong>Descripción:</strong> <span id="s_descripcion" class="vacio">--------------------</span></p>
        <p><strong>Clasificación:</strong> <span id="s_clasificacion" class="vacio">--------------------</span></p>
        <p><strong>Tipo:</strong> <span id="s_tipo" class="vacio">--------------------</span></p>
        <p><strong>Género:</strong> <span id="s_genero" class="vacio">--------------------</span></p>
        <p><strong>Dados:</strong> <span id="s_dado" class="vacio">--------------------</span></p>
    </div>

    <!-- DUNGEON MASTER -->
    <h2>Dungeon Master</h2>

    <label for="select-dm"><strong>DM:</strong></label>
    <select id="select-dm" name="ID_DM" required>
        <option value="">--- Selecciona un DM ---</option>
        <!-- Vue llenará estas opciones dinámicamente -->
    </select>

    <!-- Formulario adicional para nuevo DM (oculto por defecto) -->
    <div id="div-nuevoDm" class="div-form" style="display:none;">
        <h3>Nuevo DM</h3>
        
        <label for="lbl-nombre">Nombre:</label>
        <input type="text" id="lbl-nombre" name="lbl-nombre">

        <label for="lbl-fecha-nac">Fecha de nacimiento:</label>
        <input type="date" id="lbl-fecha-nac" name="lbl-fecha-nac">

        <p class="notas"><strong>Nota:</strong> Si el DM ya existe, no se creará otro.</p>
    </div>
    
    <!-- DATOS DE LA PARTIDA -->
    <h2>Datos de la partida</h2>
    
    <div class="div-form">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="Titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>

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

<!-- Vue.js 3 CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>

<!-- Script Vue externo para el formulario -->
<script src="/assets/js/form_partida_vue.js"></script>
