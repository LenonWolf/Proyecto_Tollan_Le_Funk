document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        mounted() {
            // Agregar event listeners a todos los botones de acción
            this.inicializarBotones();
        },
        methods: {
            inicializarBotones() {
                const tbody = document.getElementById('tabla-partidas');
                
                // Delegación de eventos: escuchar clicks en el tbody
                tbody.addEventListener('click', (e) => {
                    // Si es un botón de acción
                    const boton = e.target.closest('button[data-accion]');
                    if (boton && !boton.disabled) {
                        e.preventDefault();
                        const id = boton.dataset.id;
                        const accion = boton.dataset.accion;
                        this.ejecutarAccion(id, accion, boton);
                    }

                    // Si es el botón de modificar
                    const enlace = e.target.closest('a[data-modificar]');
                    if (enlace && !enlace.classList.contains('disabled')) {
                        e.preventDefault();
                        const id = enlace.dataset.modificar;
                        this.abrirModificar(id);
                    }
                });
            },

            async ejecutarAccion(id, accion, boton) {
                const fila = document.querySelector(`tr[data-partida-id="${id}"]`);
                if (!fila) return;

                // Activar estado de carga
                boton.classList.add('loading-opacity');
                boton.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('accion', accion);

                    const response = await fetch('src/acciones_partida.php', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Actualizar el estado visual
                        this.actualizarEstadoPartida(id, data.nuevoEstado);
                    } else {
                        alert('Error: ' + data.mensaje);
                        boton.classList.remove('loading-opacity');
                        boton.disabled = false;
                    }
                } catch (error) {
                    console.error('Error en la petición:', error);
                    alert('Error al procesar la acción');
                    boton.classList.remove('loading-opacity');
                    boton.disabled = false;
                }
            },

            actualizarEstadoPartida(id, nuevoEstado) {
                const fila = document.querySelector(`tr[data-partida-id="${id}"]`);
                if (!fila) return;

                // Actualizar el data-estado y la celda de estado
                fila.dataset.estado = nuevoEstado;
                const celdaEstado = fila.querySelector('.estado-cell');
                if (celdaEstado) {
                    celdaEstado.textContent = nuevoEstado;
                }

                // Regenerar los botones según el nuevo estado
                this.regenerarBotones(fila, id, nuevoEstado);
            },

            regenerarBotones(fila, id, estado) {
                // Botón Pausar/Reanudar
                const celdaPausar = fila.querySelector('.btn-pausar-cell');
                if (estado === 'Pausada') {
                    celdaPausar.innerHTML = `<button class='btn-edit' data-id='${id}' data-accion='reanudar'>
                        <i class='fas fa-play'></i> Reanudar
                    </button>`;
                } else {
                    const deshabilitado = ['Finalizada', 'Nueva', 'Cancelada'].includes(estado) ? 'disabled' : '';
                    celdaPausar.innerHTML = `<button class='btn-edit' data-id='${id}' data-accion='pausar' ${deshabilitado}>
                        <i class='fas fa-pause'></i> Pausar
                    </button>`;
                }

                // Botón Finalizar
                const celdaFinalizar = fila.querySelector('.btn-finalizar-cell');
                const deshabilitadoFinalizar = ['Finalizada', 'Cancelada', 'Nueva'].includes(estado) ? 'disabled' : '';
                celdaFinalizar.innerHTML = `<button class='btn-edit' data-id='${id}' data-accion='finalizar' ${deshabilitadoFinalizar}>
                    <i class='fas fa-check'></i> Finalizar
                </button>`;

                // Botón Cancelar
                const celdaCancelar = fila.querySelector('.btn-cancelar-cell');
                const deshabilitadoCancelar = ['Finalizada', 'Cancelada'].includes(estado) ? 'disabled' : '';
                celdaCancelar.innerHTML = `<button class='btn-edit' data-id='${id}' data-accion='cancelar' ${deshabilitadoCancelar}>
                    <i class='fas fa-ban'></i> Cancelar
                </button>`;

                // Botón Modificar
                const celdaModificar = fila.querySelector('.btn-modificar-cell');
                if (['Finalizada', 'Cancelada'].includes(estado)) {
                    celdaModificar.innerHTML = `<a class='btn-edit disabled'>
                        <i class='fas fa-edit'></i> Modificar
                    </a>`;
                } else {
                    celdaModificar.innerHTML = `<a class='btn-edit' href='src/modificar_partida.php?id=${id}' data-modificar='${id}'>
                        <i class='fas fa-edit'></i> Modificar
                    </a>`;
                }
            },

            abrirModificar(id) {
                window.open(
                    'src/modificar_partida.php?id=' + id,
                    'modificar',
                    'width=1000,height=600,scrollbars=yes'
                );
            }
        }
    }).mount('main');
});