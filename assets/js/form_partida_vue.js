document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                sistemas: [],
                dms: [],
                sistemaSeleccionado: '',
                dmSeleccionado: '',
                infoSistema: {
                    descripcion: '',
                    clasificacion: '',
                    tipo: '',
                    genero: [],
                    dado: []
                },
                mostrarNuevoDM: false,
                cargandoSistemas: false,
                cargandoDMs: false,
                cargandoInfo: false,
                basePath: ''
            }
        },
        mounted() {
            this.detectarBasePath();
            this.cargarSistemas();
            this.cargarDMs();
            
            const sistemaInicial = document.getElementById('data-sistema-inicial')?.value;
            const dmInicial = document.getElementById('data-dm-inicial')?.value;
            
            if (sistemaInicial) {
                this.sistemaSeleccionado = sistemaInicial;
            }
            if (dmInicial) {
                this.dmSeleccionado = dmInicial;
            }

            this.inicializarSelects();
        },
        watch: {
            sistemaSeleccionado(nuevoId) {
                if (nuevoId) {
                    this.cargarInfoSistema(nuevoId);
                } else {
                    this.limpiarInfoSistema();
                }
            },
            dmSeleccionado(nuevoId) {
                this.mostrarNuevoDM = (nuevoId === 'new' || nuevoId === '');
                this.actualizarRequeridosNuevoDM();
            }
        },
        methods: {
            detectarBasePath() {
                const hostname = window.location.hostname;
                this.basePath = hostname.includes('azurewebsites.net') ? '/' : '/Tollan_Le_Funk/';
            },
            
            url(path) {
                path = path.replace(/^\/+/, '');
                return this.basePath + path;
            },

            async cargarSistemas() {
                this.cargandoSistemas = true;
                try {
                    const url = this.url('src/api/get_sistemas.php');
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.sistemas = data.sistemas;
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
                    const url = this.url('src/api/get_dms.php');
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.dms = data.dms;
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
                    const url = this.url(`src/get_sistema.php?id=${encodeURIComponent(id)}`);
                    const response = await fetch(url);
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

            inicializarSelects() {
                const selectSistema = document.getElementById('select-sistema');
                const selectDM = document.getElementById('select-dm');
                
                selectSistema.addEventListener('change', (e) => {
                    this.sistemaSeleccionado = e.target.value;
                });
                
                selectDM.addEventListener('change', (e) => {
                    this.dmSeleccionado = e.target.value;
                });
            },

            actualizarSelectSistemas() {
                const select = document.getElementById('select-sistema');
                
                while (select.options.length > 1) {
                    select.remove(1);
                }
                
                this.sistemas.forEach(sistema => {
                    const option = document.createElement('option');
                    option.value = sistema.id;
                    option.textContent = sistema.titulo;
                    if (sistema.id == this.sistemaSeleccionado) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                if (this.sistemaSeleccionado) {
                    this.cargarInfoSistema(this.sistemaSeleccionado);
                }
            },

            actualizarSelectDMs() {
                const select = document.getElementById('select-dm');
                
                while (select.options.length > 1) {
                    select.remove(1);
                }
                
                this.dms.forEach(dm => {
                    const option = document.createElement('option');
                    option.value = dm.id;
                    option.textContent = dm.nombre;
                    if (dm.id == this.dmSeleccionado) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
                
                const optionNuevo = document.createElement('option');
                optionNuevo.value = 'new';
                optionNuevo.textContent = 'Agregar nuevo DM +';
                select.appendChild(optionNuevo);
                
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