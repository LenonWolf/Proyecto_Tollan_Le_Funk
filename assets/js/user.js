// Sistema de gestión de perfil de usuario con validaciones Vue.js

document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                // Datos de edición de usuario
                nombre: '',
                correo: '',
                correoOriginal: '',
                
                // Datos de cambio de contraseña
                passwordActual: '',
                nuevaPassword: '',
                confirmarPassword: '',
                
                // Datos de eliminación
                confirmarDelete: '',
                confirmarEliminacion: '',
                
                // Estados
                enviandoEdicion: false,
                enviandoPassword: false,
                enviandoEliminacion: false,
                verificandoEmail: false,
                emailDisponible: null,
                debounceTimer: null
            }
        },
        computed: {
            // Validación de email
            emailValido() {
                if (!this.correo) return false;
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(this.correo);
            },
            
            // Verificar si el correo cambió
            correoCambio() {
                return this.correo !== this.correoOriginal;
            },
            
            // Validar formulario de edición
            formularioEdicionValido() {
                if (!this.nombre || this.nombre.length < 3) return false;
                if (!this.emailValido) return false;
                if (this.correoCambio && this.emailDisponible !== true) return false;
                return true;
            },
            
            // Validar contraseñas coinciden
            passwordsCoinciden() {
                if (!this.nuevaPassword || !this.confirmarPassword) return null;
                return this.nuevaPassword === this.confirmarPassword;
            },
            
            // Validar formulario de contraseña
            formularioPasswordValido() {
                return this.passwordActual.length > 0 &&
                       this.nuevaPassword.length >= 6 &&
                       this.passwordsCoinciden === true;
            },
            
            // Validar formulario de eliminación
            formularioEliminacionValido() {
                const upperConfirm = this.confirmarDelete.toUpperCase().trim();
                return upperConfirm === 'ELIMINAR' &&
                       this.confirmarEliminacion.length > 0;
            },
            
            // Mensajes
            mensajeNombre() {
                if (!this.nombre) return '';
                if (this.nombre.length < 3) return 'El nombre debe tener al menos 3 caracteres';
                return '';
            },
            
            mensajeCorreo() {
                if (!this.correo) return '';
                if (!this.emailValido) return 'Formato de email inválido';
                if (this.correoCambio) {
                    if (this.verificandoEmail) return 'Verificando...';
                    if (this.emailDisponible === true) return '✓ Correo disponible';
                    if (this.emailDisponible === false) return '✗ Este correo ya está registrado';
                }
                return '';
            },
            
            claseCorreo() {
                if (this.verificandoEmail) return 'mensaje-info';
                if (this.emailDisponible === true) return 'mensaje-exito';
                if (this.emailDisponible === false) return 'mensaje-error';
                return '';
            },
            
            mensajeNuevaPassword() {
                if (!this.nuevaPassword) return '';
                if (this.nuevaPassword.length < 6) return 'La contraseña debe tener al menos 6 caracteres';
                return '';
            },
            
            mensajeConfirmarPassword() {
                if (!this.confirmarPassword) return '';
                if (this.passwordsCoinciden === false) return '✗ Las contraseñas no coinciden';
                if (this.passwordsCoinciden === true) return '✓ Las contraseñas coinciden';
                return '';
            },
            
            claseConfirmarPassword() {
                if (this.passwordsCoinciden === false) return 'mensaje-error';
                if (this.passwordsCoinciden === true) return 'mensaje-exito';
                return '';
            },
            
            mensajeConfirmarDelete() {
                if (!this.confirmarDelete) return '';
                const upperConfirm = this.confirmarDelete.toUpperCase().trim();
                if (upperConfirm !== 'ELIMINAR') {
                    return 'Debes escribir exactamente "ELIMINAR"';
                }
                return '✓ Confirmación correcta';
            },
            
            claseConfirmarDelete() {
                if (!this.confirmarDelete) return '';
                const upperConfirm = this.confirmarDelete.toUpperCase().trim();
                if (upperConfirm !== 'ELIMINAR') return 'mensaje-error';
                return 'mensaje-exito';
            }
        },
        watch: {
            correo(nuevoCorreo) {
                if (this.correoCambio && this.emailValido) {
                    this.emailDisponible = null;
                    this.verificarEmailDisponible(nuevoCorreo);
                } else if (!this.correoCambio) {
                    this.emailDisponible = null;
                }
                this.actualizarMensajes();
            },
            
            // Watch para confirmarDelete y confirmarEliminacion
            confirmarDelete() {
                this.actualizarMensajes();
            },
            
            confirmarEliminacion() {
                this.actualizarMensajes();
            }
        },
        mounted() {
            this.inicializarFormularios();
            this.actualizarMensajes();
        },
        methods: {
            inicializarFormularios() {
                // Formulario de edición
                const formEdicion = document.getElementById('form-editar-usuario');
                const inputNombre = document.getElementById('nombre');
                const inputCorreo = document.getElementById('correo');
                
                this.nombre = inputNombre.value;
                this.correo = inputCorreo.value;
                this.correoOriginal = inputCorreo.value;
                
                inputNombre.addEventListener('input', (e) => {
                    this.nombre = e.target.value;
                    this.actualizarMensajes();
                });
                
                inputCorreo.addEventListener('input', (e) => {
                    this.correo = e.target.value;
                });
                
                formEdicion.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.guardarCambios();
                });
                
                // Formulario de contraseña
                const formPassword = document.getElementById('form-cambiar-password');
                const inputPasswordActual = document.getElementById('password_actual');
                const inputNuevaPassword = document.getElementById('nueva_password');
                const inputConfirmarPassword = document.getElementById('confirmar_password');
                
                inputPasswordActual.addEventListener('input', (e) => {
                    this.passwordActual = e.target.value;
                });
                
                inputNuevaPassword.addEventListener('input', (e) => {
                    this.nuevaPassword = e.target.value;
                    this.actualizarMensajes();
                });
                
                inputConfirmarPassword.addEventListener('input', (e) => {
                    this.confirmarPassword = e.target.value;
                    this.actualizarMensajes();
                });
                
                formPassword.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.cambiarPassword();
                });
                
                // Formulario de eliminación (solo si existe - usuarios normales)
                const formEliminacion = document.getElementById('form-eliminar-usuario');
                if (formEliminacion) {
                    const inputConfirmarDelete = document.getElementById('confirmar_delete');
                    const inputConfirmarEliminacion = document.getElementById('confirmar_eliminacion');
                    
                    inputConfirmarDelete.addEventListener('input', (e) => {
                        this.confirmarDelete = e.target.value;
                        this.actualizarMensajes();
                    });
                    
                    inputConfirmarEliminacion.addEventListener('input', (e) => {
                        this.confirmarEliminacion = e.target.value;
                    });
                    
                    formEliminacion.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.eliminarCuenta();
                    });
                }
            },
            
            async verificarEmailDisponible(email) {
                clearTimeout(this.debounceTimer);
                
                this.debounceTimer = setTimeout(async () => {
                    this.verificandoEmail = true;
                    this.actualizarMensajes();
                    
                    try {
                        const response = await fetch(`/Tollan_Le_Funk/src/api/check_email.php?email=${encodeURIComponent(email)}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            this.emailDisponible = !data.existe;
                        }
                    } catch (error) {
                        console.error('Error al verificar email:', error);
                        this.emailDisponible = null;
                    } finally {
                        this.verificandoEmail = false;
                        this.actualizarMensajes();
                    }
                }, 500);
            },
            
            async guardarCambios() {
                if (!this.formularioEdicionValido) {
                    alert('Por favor, completa correctamente todos los campos');
                    return;
                }
                
                this.enviandoEdicion = true;
                const btnGuardar = document.getElementById('btn-guardar-cambios');
                btnGuardar.disabled = true;
                btnGuardar.textContent = 'Guardando...';
                
                try {
                    const formData = new FormData();
                    formData.append('nombre', this.nombre);
                    formData.append('correo', this.correo);
                    
                    const response = await fetch('/Tollan_Le_Funk/src/procesar_edicion_usuario.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('✓ ' + data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al guardar los cambios');
                } finally {
                    this.enviandoEdicion = false;
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = 'Guardar Cambios';
                }
            },
            
            async cambiarPassword() {
                if (!this.formularioPasswordValido) {
                    alert('Por favor, completa correctamente todos los campos');
                    return;
                }
                
                this.enviandoPassword = true;
                const btnPassword = document.getElementById('btn-cambiar-password');
                btnPassword.disabled = true;
                btnPassword.textContent = 'Cambiando...';
                
                try {
                    const formData = new FormData();
                    formData.append('password_actual', this.passwordActual);
                    formData.append('nueva_password', this.nuevaPassword);
                    
                    const response = await fetch('/Tollan_Le_Funk/src/procesar_cambio_password.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('✓ ' + data.message);
                        // Limpiar campos
                        this.passwordActual = '';
                        this.nuevaPassword = '';
                        this.confirmarPassword = '';
                        document.getElementById('password_actual').value = '';
                        document.getElementById('nueva_password').value = '';
                        document.getElementById('confirmar_password').value = '';
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al cambiar la contraseña');
                } finally {
                    this.enviandoPassword = false;
                    btnPassword.disabled = false;
                    btnPassword.textContent = 'Cambiar Contraseña';
                }
            },
            
            async eliminarCuenta() {
                if (!this.formularioEliminacionValido) {
                    alert('Por favor, completa correctamente la confirmación');
                    return;
                }
                
                const confirmar = confirm('⚠️ ADVERTENCIA ⚠️\n\n¿Estás COMPLETAMENTE SEGURO de que deseas eliminar tu cuenta?\n\nEsta acción es IRREVERSIBLE y perderás:\n- Todos tus datos\n- Tu historial\n- Acceso a la plataforma\n\n¿Deseas continuar?');
                
                if (!confirmar) return;
                
                this.enviandoEliminacion = true;
                const btnEliminar = document.getElementById('btn-eliminar-cuenta');
                btnEliminar.disabled = true;
                btnEliminar.textContent = 'Eliminando...';
                
                try {
                    const formData = new FormData();
                    formData.append('confirmar_delete', this.confirmarDelete);
                    formData.append('password', this.confirmarEliminacion);
                    
                    const response = await fetch('/Tollan_Le_Funk/src/procesar_eliminacion_usuario.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('Tu cuenta ha sido eliminada. Serás redirigido al inicio.');
                        window.location.href = '/Tollan_Le_Funk/index.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar la cuenta');
                } finally {
                    this.enviandoEliminacion = false;
                    btnEliminar.disabled = false;
                    btnEliminar.textContent = 'Eliminar Cuenta';
                }
            },
            
            actualizarMensajes() {
                // Mensaje nombre
                const spanNombre = document.getElementById('mensaje-nombre');
                if (spanNombre) {
                    spanNombre.textContent = this.mensajeNombre;
                    spanNombre.className = this.mensajeNombre ? 'mensaje-error' : '';
                }
                
                // Mensaje correo
                const spanCorreo = document.getElementById('mensaje-correo');
                if (spanCorreo) {
                    spanCorreo.textContent = this.mensajeCorreo;
                    spanCorreo.className = this.claseCorreo;
                }
                
                // Mensaje nueva contraseña
                const spanNuevaPass = document.getElementById('mensaje-nueva-pass');
                if (spanNuevaPass) {
                    spanNuevaPass.textContent = this.mensajeNuevaPassword;
                    spanNuevaPass.className = this.mensajeNuevaPassword ? 'mensaje-error' : '';
                }
                
                // Mensaje confirmar contraseña
                const spanConfirmarPass = document.getElementById('mensaje-confirmar-pass');
                if (spanConfirmarPass) {
                    spanConfirmarPass.textContent = this.mensajeConfirmarPassword;
                    spanConfirmarPass.className = this.claseConfirmarPassword;
                }
                
                // Mensaje confirmar delete (solo si existe)
                const spanConfirmarDelete = document.getElementById('mensaje-confirmar-delete');
                if (spanConfirmarDelete) {
                    spanConfirmarDelete.textContent = this.mensajeConfirmarDelete;
                    spanConfirmarDelete.className = this.claseConfirmarDelete;
                }
                
                // Actualizar botones
                const btnGuardar = document.getElementById('btn-guardar-cambios');
                if (btnGuardar) {
                    btnGuardar.disabled = !this.formularioEdicionValido || this.enviandoEdicion;
                }
                
                const btnPassword = document.getElementById('btn-cambiar-password');
                if (btnPassword) {
                    btnPassword.disabled = !this.formularioPasswordValido || this.enviandoPassword;
                }
                
                const btnEliminar = document.getElementById('btn-eliminar-cuenta');
                if (btnEliminar) {
                    btnEliminar.disabled = !this.formularioEliminacionValido || this.enviandoEliminacion;
                }
            }
        }
    }).mount('main');
});