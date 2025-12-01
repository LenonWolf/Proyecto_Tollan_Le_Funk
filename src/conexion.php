<?php
/**
 * Clase Conexion
 * 
 * Gestiona conexiones a MySQL con soporte para Azure Database
 * El usuario y contraseña se pasan al crear la instancia.
 */
class Conexion {
    
    private $host = "tollan-le-funk-server.mysql.database.azure.com";
    private $user;
    private $password;
    private $database = "tollan";
    private $port = 3306;
    private $conn;
    
    /**
     * Constructor
     * 
     * @param string $user Usuario de MySQL
     * @param string $password Contraseña del usuario MySQL
     */
    public function __construct($user, $password) {
        $this->user = $user;
        $this->password = $password;
    }
    
    /**
     * Método para abrir conexión con SSL para Azure
     * 
     * @return mysqli Objeto de conexión MySQLi
     */
    public function conectar() {
        // Inicializar conexión
        $this->conn = mysqli_init();
        
        if (!$this->conn) {
            die("Error al inicializar mysqli");
        }

        // Ruta al certificado CA
        // Ruta absoluta en App Service
        $certPath = "/home/site/wwwroot/certs/DigiCertGlobalRootCA.crt.pem";

        // Fallback relativo (por si corres en local)
        if (!file_exists($certPath)) {
            $certPath = __DIR__ . "/certs/DigiCertGlobalRootCA.crt.pem";
        }
        
        // Verificar que el certificado existe
        if (!file_exists($certPath)) {
            die("Error: No se encontró el certificado SSL en: " . $certPath);
        }

        // Configurar SSL
        mysqli_ssl_set(
            $this->conn,
            null,      // clave cliente (key)
            null,      // certificado cliente (cert)
            $certPath, // certificado CA
            null,      // directorio de certificados (capath)
            null       // cipher
        );

        // Intentar conectar con SSL obligatorio
        $conectado = mysqli_real_connect(
            $this->conn,
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port,
            null,
            MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
        );

        if (!$conectado) {
            die("Conexión fallida: " . mysqli_connect_error() . " (Código: " . mysqli_connect_errno() . ")");
        }

        // Establecer charset UTF-8
        if (!$this->conn->set_charset("utf8mb4")) {
            error_log("Error al establecer charset: " . $this->conn->error);
        }

        return $this->conn;
    }
    
    /**
     * Método para cerrar conexión
     */
    public function cerrar() {
        if ($this->conn && $this->conn instanceof mysqli) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    /**
     * Obtener conexión actual
     * 
     * @return mysqli|null
     */
    public function getConexion() {
        return $this->conn;
    }
    
    /**
     * Verificar si la conexión está activa
     * 
     * @return bool
     */
    public function estaConectado() {
        return ($this->conn && $this->conn->ping());
    }
}
?>