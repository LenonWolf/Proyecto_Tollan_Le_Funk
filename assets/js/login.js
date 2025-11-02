// Sistema de login con validaciones AJAX

document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                email: '',
                password: '',
                enviando: false,
                redirect: null
            }
        },
        mounted() {
            // Obtener el parámetro redirect de la URL si existe
            const urlParams = new URLSearchParams(window.location.search);
            this.redirect = urlParams.get('redirect');
            
            this.inicializarFormulario();
        },
        methods: {
            inicializarFormulario() {
                const form = document.getElementById('form-login');
                
                // Prevenir el submit normal del formulario
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.iniciarSesion();
                });
                
                // Sincronizar inputs con Vue
                const inputEmail = document.getElementById('email');
                const inputPassword = document.getElementById('password');
                
                inputEmail.addEventListener('input', (e) => {
                    this.email = e.target.value;
                });
                
                inputPassword.addEventListener('input', (e) => {
                    this.password = e.target.value;
                });
            },
            
            async iniciarSesion() {
                // Validar campos
                if (!this.email || !this.password) {
                    alert('Por favor, completa todos los campos');
                    return;
                }
                
                this.enviando = true;
                const btnLogin = document.getElementById('btn-login');
                btnLogin.disabled = true;
                btnLogin.textContent = 'Iniciando sesión...';
                
                try {
                    const formData = new FormData();
                    formData.append('email', this.email);
                    formData.append('password', this.password);
                    
                    // Agregar redirect si existe
                    if (this.redirect) {
                        formData.append('redirect', this.redirect);
                    }
                    
                    const response = await fetch('/Tollan_Le_Funk/src/procesar_login.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert(data.message);
                        window.location.href = data.redirect;
                    } else {
                        alert('Error: ' + data.message);
                        btnLogin.disabled = false;
                        btnLogin.textContent = 'Iniciar Sesión';
                    }
                } catch (error) {
                    console.error('Error en el login:', error);
                    alert('Error al procesar el inicio de sesión. Inténtalo de nuevo.');
                    btnLogin.disabled = false;
                    btnLogin.textContent = 'Iniciar Sesión';
                } finally {
                    this.enviando = false;
                }
            }
        }
    }).mount('main');
});