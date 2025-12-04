<?php
/**
 * Clase Conexion
 * 
 * Gestiona conexiones simples a MySQL con diferentes usuarios.
 * El usuario y contraseña se pasan al crear la instancia.
 */
class Conexion {
    
    private $host = "";
    private $user = "";
    private $password = "";
    private $database = "";
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
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
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