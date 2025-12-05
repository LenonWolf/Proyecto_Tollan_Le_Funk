// Sistema de login con validaciones AJAX

document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                email: '',
                password: '',
                enviando: false,
                redirect: null,
                basePath: '' // Ruta base del proyecto
            }
        },
        mounted() {
            // Detectar la ruta base del proyecto
            this.detectarBasePath();
            
            // Obtener el parámetro redirect de la URL si existe
            const urlParams = new URLSearchParams(window.location.search);
            this.redirect = urlParams.get('redirect');
            
            this.inicializarFormulario();
        },
        methods: {
            /**
             * Detectar la ruta base según el dominio
             */
            detectarBasePath() {
                const hostname = window.location.hostname;
                
                // Si está en Azure (.azurewebsites.net), la ruta base es /
                if (hostname.includes('azurewebsites.net')) {
                    this.basePath = '/';
                } else {
                    // En local, usar /Tollan_Le_Funk/
                    this.basePath = '/Tollan_Le_Funk/';
                }
            },
            
            /**
             * Generar URL correcta según el entorno
             */
            url(path) {
                // Remover slash inicial si existe
                path = path.replace(/^\/+/, '');
                return this.basePath + path;
            },
            
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
                    
                    // Usar ruta dinámica según el entorno
                    const url = this.url('src/procesar_login.php');
                    console.log('Intentando conectar a:', url); // Para debug
                    
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData
                    });
                    
                    // Verificar si la respuesta es correcta
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await response.text();
                        console.error('Respuesta no JSON:', text);
                        throw new Error('La respuesta del servidor no es JSON válido');
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert(data.message);
                        // Redirigir usando la URL que viene del servidor
                        window.location.href = data.redirect;
                    } else {
                        alert('Error: ' + data.message);
                        btnLogin.disabled = false;
                        btnLogin.textContent = 'Iniciar Sesión';
                    }
                } catch (error) {
                    console.error('Error en el login:', error);
                    alert('Error al procesar el inicio de sesión. Inténtalo de nuevo.\n\nDetalle técnico: ' + error.message);
                    btnLogin.disabled = false;
                    btnLogin.textContent = 'Iniciar Sesión';
                } finally {
                    this.enviando = false;
                }
            }
        }
    }).mount('main');
});