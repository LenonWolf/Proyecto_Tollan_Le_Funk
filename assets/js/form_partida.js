/*******************************************************************
* - Mostrar información del sistema seleccionado (via fetch JSON). *
* - Alternar la visibilidad del formulario para crear un nuevo DM. *
* - Pre-cargar estado inicial al cargar la página.                 *
*******************************************************************/

// Función auxiliar para pintar un valor en un elemento y aplicar clases según si el contenido es válido o vacío.
// - el: nodo destino (span con info del sistema)
// - valor: texto a mostrar; si es falsy o '--------------------', se marca como vacío.
function setValor(el, valor) {
    if (!valor || valor === '--------------------') {
        el.textContent = '--------------------'; // Placeholder visual cuando no hay datos
        el.classList.remove('valido'); // Quitar marca "valido"
        el.classList.add('vacio'); // Añadir marca "vacio"
    } else {
        el.textContent = valor; // Mostrar el valor real
        el.classList.remove('vacio'); // Quitar marca "vacio"
        el.classList.add('valido'); // Añadir marca "valido"
    }
}

/***********************************************
* CARGA DE INFORMACIÓN DEL SISTEMA (AJAX/JSON) *
***********************************************/

// Lógica asíncrona para consultar datos del sistema y pintarlos.
// - id: identificador del sistema seleccionado (value del <select>).
async function cargarInfoSistema(id) {
    // Referencias a los nodos donde se mostrará cada fragmento de información
    const descEl = document.getElementById('s_descripcion');
    const clasEl = document.getElementById('s_clasificacion');
    const tipoEl = document.getElementById('s_tipo');
    const geneEl = document.getElementById('s_genero');
    const dadoEl = document.getElementById('s_dado');

    // Si no hay ID (select vacío), limpiar todos los campos con placeholder
    if (!id) {
        setValor(descEl, '--------------------');
        setValor(clasEl, '--------------------');
        setValor(tipoEl, '--------------------');
        setValor(geneEl, '--------------------');
        setValor(dadoEl, '--------------------');
        return; // No se hace petición al servidor
    }

    try {
        // Petición al endpoint que devuelve JSON con la info del sistema
        // encodeURIComponent asegura que el ID se envíe seguro en la URL.
        const resp = await fetch('/Tollan_Le_Funk/src/get_sistema.php?id=' + encodeURIComponent(id));

        // Convertir la respuesta HTTP a objeto JS (espera JSON puro)
        const data = await resp.json();

        // Si el backend indica éxito, pintar cada campo;
        // usar fallback '--------------------' cuando venga nulo o vacío.
        if (data.success) {
            setValor(descEl, data.descripcion ?? '--------------------');
            setValor(clasEl, data.clasificacion ?? '--------------------');
            setValor(tipoEl, data.tipo ?? '--------------------');

            // Genero y dado pueden ser arreglos; si tienen elementos, unir por coma,
            // si no, mostrar placeholder.
            setValor(geneEl, (data.genero?.length) ? data.genero.join(', ') : '--------------------');
            setValor(dadoEl, (data.dado?.length) ? data.dado.join(', ') : '--------------------');
        } else {
            // Caso de error funcional reportado por el backend (success=false)
            setValor(descEl, 'Error al cargar');
            setValor(clasEl, '--------------------');
            setValor(tipoEl, '--------------------');
            setValor(geneEl, '--------------------');
            setValor(dadoEl, '--------------------');
        }
    } catch {
        // Errores de red (servidor caído, ruta mal, JSON inválido, etc.)
        // Se notifica en la descripción; el resto se mantiene como estaba.
        setValor(descEl, 'Error de red');
    }
}

// Al cambiar el <select id="select-sistema">, cargar información del sistema elegido.
// 'this.value' es el ID del sistema seleccionado.
document.getElementById('select-sistema').addEventListener('change', function() {
    cargarInfoSistema(this.value);
});

/***********************************
* VISUALIZAR FORMULARIO "NUEVO DM" *
***********************************/

// Mostrar/ocultar el bloque para crear un nuevo DM según el valor del select.
// También alterna la obligatoriedad (required) de los campos del nuevo DM.
function mostrarNuevoDM() {
    const selectDM = document.getElementById('select-dm'); // <select> con lista de DMs
    const nuevo = document.getElementById('div-nuevoDm'); // Contenedor del formulario "Nuevo DM"

    if (selectDM.value === 'new') {
        // Si el usuario elige "Agregar nuevo DM +", mostrar el formulario adicional
        nuevo.style.display = 'block';
        // Marcar como obligatorios los campos necesarios para crear el nuevo DM
        document.getElementById('lbl-nombre').required = true;
        document.getElementById('lbl-fecha-nac').required = true;
    } else {
        // Si el usuario selecciona un DM existente, ocultar el formulario adicional
        nuevo.style.display = 'none';
        // Y desmarcar como obligatorios esos campos (no aplican)
        document.getElementById('lbl-nombre').required = false;
        document.getElementById('lbl-fecha-nac').required = false;
    }
}

// Registrar el listener para que la visibilidad cambie al seleccionar otro DM
document.getElementById('select-dm').addEventListener('change', mostrarNuevoDM);

// Al terminar de construir el DOM (HTML cargado), inicializar:
// - Si ya hay un sistema preseleccionado (modo edición), cargar su información.
// - Ajustar la visibilidad del bloque "Nuevo DM" según el valor actual del select.
window.addEventListener('DOMContentLoaded', () => {
    const selectSistema = document.getElementById('select-sistema');

    // Precarga de información del sistema si existe un valor inicial
    if (selectSistema.value) {
        cargarInfoSistema(selectSistema.value);
    }

    // Sincronizar la interfaz del "Nuevo DM" con el valor actual del select
    mostrarNuevoDM();
});