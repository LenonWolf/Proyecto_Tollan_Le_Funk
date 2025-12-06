/*******************************************************************
* - Mostrar información del sistema seleccionado (via fetch JSON). *
* - Alternar la visibilidad del formulario para crear un nuevo DM. *
* - Pre-cargar estado inicial al cargar la página.                 *
*******************************************************************/

// Detectar ruta base del proyecto
function getBasePath() {
    const hostname = window.location.hostname;
    return hostname.includes('azurewebsites.net') ? '/' : '/Tollan_Le_Funk/';
}

function url(path) {
    path = path.replace(/^\/+/, '');
    return getBasePath() + path;
}

function setValor(el, valor) {
    if (!valor || valor === '--------------------') {
        el.textContent = '--------------------';
        el.classList.remove('valido');
        el.classList.add('vacio');
    } else {
        el.textContent = valor;
        el.classList.remove('vacio');
        el.classList.add('valido');
    }
}

async function cargarInfoSistema(id) {
    const descEl = document.getElementById('s_descripcion');
    const clasEl = document.getElementById('s_clasificacion');
    const tipoEl = document.getElementById('s_tipo');
    const geneEl = document.getElementById('s_genero');
    const dadoEl = document.getElementById('s_dado');

    if (!id) {
        setValor(descEl, '--------------------');
        setValor(clasEl, '--------------------');
        setValor(tipoEl, '--------------------');
        setValor(geneEl, '--------------------');
        setValor(dadoEl, '--------------------');
        return;
    }

    try {
        const fetchUrl = url('src/get_sistema.php?id=' + encodeURIComponent(id));
        const resp = await fetch(fetchUrl);
        const data = await resp.json();

        if (data.success) {
            setValor(descEl, data.descripcion ?? '--------------------');
            setValor(clasEl, data.clasificacion ?? '--------------------');
            setValor(tipoEl, data.tipo ?? '--------------------');
            setValor(geneEl, (data.genero?.length) ? data.genero.join(', ') : '--------------------');
            setValor(dadoEl, (data.dado?.length) ? data.dado.join(', ') : '--------------------');
        } else {
            setValor(descEl, 'Error al cargar');
            setValor(clasEl, '--------------------');
            setValor(tipoEl, '--------------------');
            setValor(geneEl, '--------------------');
            setValor(dadoEl, '--------------------');
        }
    } catch {
        setValor(descEl, 'Error de red');
    }
}

document.getElementById('select-sistema').addEventListener('change', function() {
    cargarInfoSistema(this.value);
});

function mostrarNuevoDM() {
    const selectDM = document.getElementById('select-dm');
    const nuevo = document.getElementById('div-nuevoDm');

    if (selectDM.value === 'new') {
        nuevo.style.display = 'block';
        document.getElementById('lbl-nombre').required = true;
        document.getElementById('lbl-fecha-nac').required = true;
    } else {
        nuevo.style.display = 'none';
        document.getElementById('lbl-nombre').required = false;
        document.getElementById('lbl-fecha-nac').required = false;
    }
}

document.getElementById('select-dm').addEventListener('change', mostrarNuevoDM);

window.addEventListener('DOMContentLoaded', () => {
    const selectSistema = document.getElementById('select-sistema');

    if (selectSistema.value) {
        cargarInfoSistema(selectSistema.value);
    }

    mostrarNuevoDM();
});