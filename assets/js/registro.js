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
                emailDisponible: null,
                verificandoEmail: false,
                enviando: false,
                debounceTimer: null,
                basePath: ''
            }
        },
        computed: {
            passwordsCoinciden() {
                if (!this.password || !this.confirmPassword) return null;
                return this.password === this.confirmPassword;
            },
            
            formularioValido() {
                return this.username.length > 0 &&
                       this.emailValido &&
                       this.emailDisponible === true &&
                       this.password.length >= 6 &&
                       this.passwordsCoinciden === true;
            },
            
            emailValido() {
                if (!this.email) return false;
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(this.email);
            },
            
            mensajeUsername() {
                if (!this.username) return '';
                if (this.username.length < 3) return 'El nombre debe tener al menos 3 caracteres';
                return '';
            },
            
            mensajeEmail() {
                if (!this.email) return '';
                if (!this.emailValido) return 'Formato de email inválido';
                if (this.verificandoEmail) return 'Verificando...';
                if (this.emailDisponible === true) return '✓ Correo disponible';
                if (this.emailDisponible === false) return '✗ Este correo ya está registrado';
                return '';
            },
            
            claseEmail() {
                if (this.verificandoEmail) return 'mensaje-info';
                if (this.emailDisponible === true) return 'mensaje-exito';
                if (this.emailDisponible === false) return 'mensaje-error';
                return '';
            },
            
            mensajePassword() {
                if (!this.password) return '';
                if (this.password.length < 6) return 'La contraseña debe tener al menos 6 caracteres';
                return '';
            },
            
            mensajeConfirm() {
                if (!this.confirmPassword) return '';
                if (this.passwordsCoinciden === false) return '✗ Las contraseñas no coinciden';
                if (this.passwordsCoinciden === true) return '✓ Las contraseñas coinciden';
                return '';
            },
            
            claseConfirm() {
                if (this.passwordsCoinciden === false) return 'mensaje-error';
                if (this.passwordsCoinciden === true) return 'mensaje-exito';
                return '';
            }
        },
        watch: {
            email(nuevoEmail) {
                this.emailDisponible = null;
                
                if (this.emailValido) {
                    this.verificarEmailDisponible(nuevoEmail);
                }
            }
        },
        mounted() {
            this.detectarBasePath();
            this.inicializarFormulario();
            this.actualizarMensajes();
        },
        methods: {
            detectarBasePath() {
                const hostname = window.location.hostname;
                if (hostname.includes('azurewebsites.net')) {
                    this.basePath = '/';
                } else {
                    this.basePath = '/Tollan_Le_Funk/';
                }
            },
            
            url(path) {
                path = path.replace(/^\/+/, '');
                return this.basePath + path;
            },
            
            inicializarFormulario() {
                const form = document.getElementById('form-registro');
                
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.registrarUsuario();
                });
                
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
            
            async verificarEmailDisponible(email) {
                clearTimeout(this.debounceTimer);
                
                this.debounceTimer = setTimeout(async () => {
                    this.verificandoEmail = true;
                    this.actualizarMensajes();
                    
                    try {
                        const url = this.url(`src/api/check_email.php?email=${encodeURIComponent(email)}`);
                        const response = await fetch(url);
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
            
            async registrarUsuario() {
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
                    
                    const url = this.url('src/procesar_registro.php');
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('¡Registro exitoso! 🎉\n\nYa puedes iniciar sesión con tu cuenta.');
                        window.location.href = this.url('src/login.php');
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
            
            actualizarMensajes() {
                const mensajeUsername = document.getElementById('mensaje-username');
                if (mensajeUsername) {
                    mensajeUsername.textContent = this.mensajeUsername;
                    mensajeUsername.className = this.mensajeUsername ? 'mensaje-error' : '';
                }
                
                const mensajeEmail = document.getElementById('mensaje-email');
                if (mensajeEmail) {
                    mensajeEmail.textContent = this.mensajeEmail;
                    mensajeEmail.className = this.claseEmail;
                }
                
                const mensajePassword = document.getElementById('mensaje-password');
                if (mensajePassword) {
                    mensajePassword.textContent = this.mensajePassword;
                    mensajePassword.className = this.mensajePassword ? 'mensaje-error' : '';
                }
                
                const mensajeConfirm = document.getElementById('mensaje-confirm');
                if (mensajeConfirm) {
                    mensajeConfirm.textContent = this.mensajeConfirm;
                    mensajeConfirm.className = this.claseConfirm;
                }
                
                const btnRegistrar = document.getElementById('btn-registrar');
                if (btnRegistrar) {
                    btnRegistrar.disabled = !this.formularioValido || this.enviando;
                }
            }
        }
    }).mount('main');
});