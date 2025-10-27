document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sistemas: [],           // Lista de sistemas desde la API
                dms: [],                // Lista de DMs desde la API
                sistemaSeleccionado: '', // ID del sistema seleccionado
                dmSeleccionado: '',      // ID del DM seleccionado
                infoSistema: {          // Información del sistema actual
                    descripcion: '',
                    clasificacion: '',
                    tipo: '',
                    genero: [],
                    dado: []
                },
                mostrarNuevoDM: false,  // Flag para mostrar/ocultar formulario nuevo DM
                cargandoSistemas: false,
                cargandoDMs: false,
                cargandoInfo: false
            }
        },
        mounted() {
            // Al montar, cargar los datos iniciales
            this.cargarSistemas();
            this.cargarDMs();
            
            // Obtener valores iniciales si existen (modo edición)
            const sistemaInicial = document.getElementById('data-sistema-inicial')?.value;
            const dmInicial = document.getElementById('data-dm-inicial')?.value;
            
            if (sistemaInicial) {
                this.sistemaSeleccionado = sistemaInicial;
            }
            if (dmInicial) {
                this.dmSeleccionado = dmInicial;
            }

            // Inicializar los selects nativos
            this.inicializarSelects();
        },
        watch: {
            // Observar cambios en el sistema seleccionado
            sistemaSeleccionado(nuevoId) {
                if (nuevoId) {
                    this.cargarInfoSistema(nuevoId);
                } else {
                    this.limpiarInfoSistema();
                }
            },
            // Observar cambios en el DM seleccionado
            dmSeleccionado(nuevoId) {
                this.mostrarNuevoDM = (nuevoId === 'new' || nuevoId === '');
                this.actualizarRequeridosNuevoDM();
            }
        },
        methods: {
            /***************************
            * CARGA DE DATOS INICIALES *
            ***************************/

            async cargarSistemas() {
                this.cargandoSistemas = true;
                try {
                    const response = await fetch('/Tollan_Le_Funk/src/api/get_sistemas.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.sistemas = data.sistemas;
                        // Actualizar el select nativo
                        this.actualizarSelectSistemas();
                    }
                } catch (error) {
                    console.error('Error al cargar sistemas:', error);
                } finally {
                    this.cargandoSistemas = false;
                }
            },

            async cargarDMs() {
                this.cargandoDMs = true;
                try {
                    const response = await fetch('/Tollan_Le_Funk/src/api/get_dms.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.dms = data.dms;
                        // Actualizar el select nativo
                        this.actualizarSelectDMs();
                    }
                } catch (error) {
                    console.error('Error al cargar DMs:', error);
                } finally {
                    this.cargandoDMs = false;
                }
            },

            async cargarInfoSistema(id) {
                this.cargandoInfo = true;
                try {
                    const response = await fetch(`/Tollan_Le_Funk/src/get_sistema.php?id=${encodeURIComponent(id)}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.infoSistema = {
                            descripcion: data.descripcion ?? '--------------------',
                            clasificacion: data.clasificacion ?? '--------------------',
                            tipo: data.tipo ?? '--------------------',
                            genero: data.genero?.length ? data.genero : [],
                            dado: data.dado?.length ? data.dado : []
                        };
                        this.actualizarInfoSistemaDOM();
                    } else {
                        this.mostrarErrorInfoSistema();
                    }
                } catch (error) {
                    console.error('Error al cargar info del sistema:', error);
                    this.mostrarErrorInfoSistema();
                } finally {
                    this.cargandoInfo = false;
                }
            },

            /********************************
            * ACTUALIZACIÓN DEL DOM NATIVO *
            ********************************/

            inicializarSelects() {
                const selectSistema = document.getElementById('select-sistema');
                const selectDM = document.getElementById('select-dm');
                
                // Listeners para sincronizar con Vue
                selectSistema.addEventListener('change', (e) => {
                    this.sistemaSeleccionado = e.target.value;
                });
                
                selectDM.addEventListener('change', (e) => {
                    this.dmSeleccionado = e.target.value;
                });
            },

            actualizarSelectSistemas() {
                const select = document.getElementById('select-sistema');
                
                // Limpiar opciones existentes excepto la primera
                while (select.options.length > 1) {
                    select.remove(1);
                }
                
                // Agregar nuevas opciones
                this.sistemas.forEach(sistema => {
                    const option = document.createElement('option');
                    option.value = sistema.id;
                    option.textContent = sistema.titulo;
                    if (sistema.id == this.sistemaSeleccionado) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                // Si había un sistema preseleccionado, cargar su info
                if (this.sistemaSeleccionado) {
                    this.cargarInfoSistema(this.sistemaSeleccionado);
                }
            },

            actualizarSelectDMs() {
                const select = document.getElementById('select-dm');
                
                // Limpiar opciones existentes excepto la primera
                while (select.options.length > 1) {
                    select.remove(1);
                }
                
                // Agregar opciones de DMs existentes
                this.dms.forEach(dm => {
                    const option = document.createElement('option');
                    option.value = dm.id;
                    option.textContent = dm.nombre;
                    if (dm.id == this.dmSeleccionado) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                // Agregar opción "Nuevo DM"
                const optionNuevo = document.createElement('option');
                optionNuevo.value = 'new';
                optionNuevo.textContent = 'Agregar nuevo DM +';
                select.appendChild(optionNuevo);
                
                // Actualizar visibilidad del formulario nuevo DM
                if (this.dmSeleccionado) {
                    this.mostrarNuevoDM = (this.dmSeleccionado === 'new');
                    this.actualizarRequeridosNuevoDM();
                }
            },

            actualizarInfoSistemaDOM() {
                this.setValor('s_descripcion', this.infoSistema.descripcion);
                this.setValor('s_clasificacion', this.infoSistema.clasificacion);
                this.setValor('s_tipo', this.infoSistema.tipo);
                
                const generoTexto = this.infoSistema.genero.length 
                    ? this.infoSistema.genero.join(', ') 
                    : '--------------------';
                this.setValor('s_genero', generoTexto);
                
                const dadoTexto = this.infoSistema.dado.length 
                    ? this.infoSistema.dado.join(', ') 
                    : '--------------------';
                this.setValor('s_dado', dadoTexto);
            },

            limpiarInfoSistema() {
                this.setValor('s_descripcion', '--------------------');
                this.setValor('s_clasificacion', '--------------------');
                this.setValor('s_tipo', '--------------------');
                this.setValor('s_genero', '--------------------');
                this.setValor('s_dado', '--------------------');
            },

            mostrarErrorInfoSistema() {
                this.setValor('s_descripcion', 'Error al cargar');
                this.setValor('s_clasificacion', '--------------------');
                this.setValor('s_tipo', '--------------------');
                this.setValor('s_genero', '--------------------');
                this.setValor('s_dado', '--------------------');
            },

            /********************************
            * GESTIÓN DEL FORMULARIO NUEVO DM *
            ********************************/

            actualizarRequeridosNuevoDM() {
                const divNuevoDM = document.getElementById('div-nuevoDm');
                const inputNombre = document.getElementById('lbl-nombre');
                const inputFecha = document.getElementById('lbl-fecha-nac');
                
                if (this.mostrarNuevoDM) {
                    divNuevoDM.style.display = 'block';
                    inputNombre.required = true;
                    inputFecha.required = true;
                } else {
                    divNuevoDM.style.display = 'none';
                    inputNombre.required = false;
                    inputFecha.required = false;
                }
            },

            /**********************
            * FUNCIONES AUXILIARES *
            **********************/

            setValor(elementId, valor) {
                const el = document.getElementById(elementId);
                if (!el) return;
                
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
        }
    }).mount('#form-partida');
});