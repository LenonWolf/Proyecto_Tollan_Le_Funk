<!-- Pie de pagina con información de soporte, créditos, versión y derechos de autor -->
<footer aria-label="Pie de página con información de soporte, créditos, versión y derechos de autor">
    <p>
        <i class="fas fa-envelope"></i> Soporte: 
        <a href="mailto:soporte@tollanlef.com?subject=Ayuda%20con%20el%20gestor">soporte@tollanlef.com</a>
    </p> <!-- Información de soporte -->

    <p>
        <i class="fas fa-code"></i> Desarrollado por Juan Alberto Sanchez Hernandez y Luis Eduardo Nieves Avila
    </p> <!-- Desarrolladores -->

    <p>
        <i class="fas fa-info-circle"></i> Sistema v1.0 | 
        <a href="<?php echo fullUrl('Assets_Tollan_Le_Funk/docs/Manual.pdf'); ?>" target="_blank" rel="noopener noreferrer">
            Manual de uso
        </a>
    </p> <!-- Versión y manual -->

    <p>&copy;<?php echo date("Y"); ?> <b>Tollan le Funk</b> - Todos los Derechos Reservados.</p> <!-- Derechos de autor -->

    <?php
    $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <div id="validator">
        <p>
            <a href="https://validator.w3.org/check?uri=<?php echo urlencode($currentUrl); ?>" target="_blank" rel="noopener">
                <img style="border:0;width:88px;height:31px"
                    src="<?php echo url('assets/img/vhtml.svg'); ?>"
                    alt="¡HTML Válido!">
            </a>
        </p>
        <p>
            <a href="https://jigsaw.w3.org/css-validator/validator?uri=<?php echo urlencode($currentUrl); ?>&profile=css3svg&usermedium=all&warning=1&vextwarning=&lang=es" target="_blank" rel="noopener">
                <img style="border:0;width:88px;height:31px"
                    src="https://jigsaw.w3.org/css-validator/images/vcss-blue"
                    alt="¡CSS Válido!">
            </a>
        </p>
    </div>
</footer>