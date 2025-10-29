// Sistema de registro con validaciones en tiempo real (AJAX + Vue.js)

document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                username: '',
                email: '',
                password: '',
                confirmPassword: '',
                emailDisponible: null,  // null = no verificado, true = disponible, false = ocupado
                verificandoEmail: false,
                enviando: false,
                debounceTimer: null
            }
        },
        computed: {
            // Verificar si las contraseñas coinciden
            passwordsCoinciden() {
                if (!this.password || !this.confirmPassword) return null;
                return this.password === this.confirmPassword;
            },
            
            // Verificar si el formulario es válido
            formularioValido() {
                return this.username.length > 0 &&
                       this.emailValido &&
                       this.emailDisponible === true &&
                       this.password.length >= 6 &&
                       this.passwordsCoinciden === true;
            },
            
            // Validar formato del email
            emailValido() {
                if (!this.email) return false;
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(this.email);
            },
            
            // Mensaje para el username
            mensajeUsername() {
                if (!this.username) return '';
                if (this.username.length < 3) return 'El nombre debe tener al menos 3 caracteres';
                return '';
            },
            
            // Mensaje para el email
            mensajeEmail() {
                if (!this.email) return '';
                if (!this.emailValido) return 'Formato de email inválido';
                if (this.verificandoEmail) return 'Verificando...';
                if (this.emailDisponible === true) return '✓ Correo disponible';
                if (this.emailDisponible === false) return '✗ Este correo ya está registrado';
                return '';
            },
            
            // Clase CSS para el mensaje de email
            claseEmail() {
                if (this.verificandoEmail) return 'mensaje-info';
                if (this.emailDisponible === true) return 'mensaje-exito';
                if (this.emailDisponible === false) return 'mensaje-error';
                return '';
            },
            
            // Mensaje para la contraseña
            mensajePassword() {
                if (!this.password) return '';
                if (this.password.length < 6) return 'La contraseña debe tener al menos 6 caracteres';
                return '';
            },
            
            // Mensaje para confirmar contraseña
            mensajeConfirm() {
                if (!this.confirmPassword) return '';
                if (this.passwordsCoinciden === false) return '✗ Las contraseñas no coinciden';
                if (this.passwordsCoinciden === true) return '✓ Las contraseñas coinciden';
                return '';
            },
            
            // Clase CSS para el mensaje de confirmación
            claseConfirm() {
                if (this.passwordsCoinciden === false) return 'mensaje-error';
                if (this.passwordsCoinciden === true) return 'mensaje-exito';
                return '';
            }
        },
        watch: {
            // Verificar disponibilidad del email cuando cambia
            email(nuevoEmail) {
                this.emailDisponible = null;
                
                if (this.emailValido) {
                    this.verificarEmailDisponible(nuevoEmail);
                }
            }
        },
        mounted() {
            this.inicializarFormulario();
            this.actualizarMensajes();
        },
        methods: {
            inicializarFormulario() {
                const form = document.getElementById('form-registro');
                
                // Prevenir el submit normal del formulario
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.registrarUsuario();
                });
                
                // Sincronizar inputs con Vue
                const inputUsername = document.getElementById('username');
                const inputEmail = document.getElementById('email');
                const inputPassword = document.getElementById('password');
                const inputConfirm = document.getElementById('confirm_password');
                
                inputUsername.addEventListener('input', (e) => {
                    this.username = e.target.value;
                    this.actualizarMensajes();
                });
                
                inputEmail.addEventListener('input', (e) => {
                    this.email = e.target.value;
                    this.actualizarMensajes();
                });
                
                inputPassword.addEventListener('input', (e) => {
                    this.password = e.target.value;
                    this.actualizarMensajes();
                });
                
                inputConfirm.addEventListener('input', (e) => {
                    this.confirmPassword = e.target.value;
                    this.actualizarMensajes();
                });
            },
            
            // Verificar si el email ya está registrado (con debounce)
            async verificarEmailDisponible(email) {
                // Esperar 500ms antes de hacer la petición (debounce)
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
            
            // Registrar usuario vía AJAX
            async registrarUsuario() {
                // Validar antes de enviar
                if (!this.formularioValido) {
                    alert('Por favor, completa correctamente todos los campos');
                    return;
                }
                
                this.enviando = true;
                const btnRegistrar = document.getElementById('btn-registrar');
                btnRegistrar.disabled = true;
                btnRegistrar.textContent = 'Registrando...';
                
                try {
                    const formData = new FormData();
                    formData.append('username', this.username);
                    formData.append('email', this.email);
                    formData.append('password', this.password);
                    
                    const response = await fetch('/Tollan_Le_Funk/src/procesar_registro.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('¡Registro exitoso! 🎉\n\nYa puedes iniciar sesión con tu cuenta.');
                        window.location.href = '/Tollan_Le_Funk/src/login.php';
                    } else {
                        alert('Error: ' + data.message);
                        btnRegistrar.disabled = false;
                        btnRegistrar.textContent = 'Registrar';
                    }
                } catch (error) {
                    console.error('Error en el registro:', error);
                    alert('Error al procesar el registro. Inténtalo de nuevo.');
                    btnRegistrar.disabled = false;
                    btnRegistrar.textContent = 'Registrar';
                } finally {
                    this.enviando = false;
                }
            },
            
            // Actualizar todos los mensajes en el DOM
            actualizarMensajes() {
                // Mensaje de username
                const mensajeUsername = document.getElementById('mensaje-username');
                if (mensajeUsername) {
                    mensajeUsername.textContent = this.mensajeUsername;
                    mensajeUsername.className = this.mensajeUsername ? 'mensaje-error' : '';
                }
                
                // Mensaje de email
                const mensajeEmail = document.getElementById('mensaje-email');
                if (mensajeEmail) {
                    mensajeEmail.textContent = this.mensajeEmail;
                    mensajeEmail.className = this.claseEmail;
                }
                
                // Mensaje de password
                const mensajePassword = document.getElementById('mensaje-password');
                if (mensajePassword) {
                    mensajePassword.textContent = this.mensajePassword;
                    mensajePassword.className = this.mensajePassword ? 'mensaje-error' : '';
                }
                
                // Mensaje de confirmación
                const mensajeConfirm = document.getElementById('mensaje-confirm');
                if (mensajeConfirm) {
                    mensajeConfirm.textContent = this.mensajeConfirm;
                    mensajeConfirm.className = this.claseConfirm;
                }
                
                // Actualizar estado del botón
                const btnRegistrar = document.getElementById('btn-registrar');
                if (btnRegistrar) {
                    btnRegistrar.disabled = !this.formularioValido || this.enviando;
                }
            }
        }
    }).mount('main');
});