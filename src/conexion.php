<?php
/**
 * Clase Conexion
 * 
 * Gestiona conexiones simples a MySQL con diferentes usuarios.
 * El usuario y contraseña se pasan al crear la instancia.
 */
class Conexion {
    
    private $host = "tollan-le-funk-server.mysql.database.azure.com";
    private $user;
    private $password;
    private $database = "tollan";
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
     * Método para abrir conexión
     * 
     * @return mysqli Objeto de conexión MySQLi
     */
    public function conectar() {
        // Inicializar conexión
        $this->conn = mysqli_init();

        // Configurar SSL: ruta al certificado
        mysqli_ssl_set(
            $this->conn,
            null, // clave cliente
            null, // certificado cliente
            __DIR__ . "/certs/DigiCertGlobalRootCA.crt.pem", // certificado CA
            null, // directorio de certificados
            null  // cipher
        );

        // Conectar con SSL obligatorio
        if (!mysqli_real_connect(
            $this->conn,
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            3306,
            null,
            MYSQLI_CLIENT_SSL
        )) {
            die("Conexión fallida: " . mysqli_connect_error());
        }

        $this->conn->set_charset("utf8");
        return $this->conn;
    }
    
    /**
     * Método para cerrar conexión
     */
    public function cerrar() {
        if ($this->conn) {
            $this->conn->close();
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
}
?>