// Función auxiliar: asigna texto y clase según validez
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

// Función para cargar info del sistema
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
        const resp = await fetch('/Tollan_Le_Funk/src/get_sistema.php?id=' + encodeURIComponent(id));
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

// Evento al cambiar el select de sistema
document.getElementById('select-sistema').addEventListener('change', function() {
    cargarInfoSistema(this.value);
});

// Evento al cambiar el select de DM
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

// Al cargar la página, precargar info de sistema y estado del DM
window.addEventListener('DOMContentLoaded', () => {
    const selectSistema = document.getElementById('select-sistema');
    if (selectSistema.value) {
        cargarInfoSistema(selectSistema.value);
    }
    mostrarNuevoDM();
});