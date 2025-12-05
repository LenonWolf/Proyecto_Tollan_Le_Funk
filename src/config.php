<?php
/**
 * Archivo de configuración para gestionar rutas
 * Detecta automáticamente si está en local o en Azure
 */

// Detectar si estamos en Azure o en local
$isAzure = (strpos($_SERVER['HTTP_HOST'], 'azurewebsites.net') !== false);

// Definir la ruta base según el entorno
if ($isAzure) {
    // En Azure, el proyecto está en la raíz
    define('BASE_PATH', '/');
} else {
    // En local, el proyecto está en una subcarpeta
    define('BASE_PATH', '/Tollan_Le_Funk/');
}

// Definir el protocolo (http o https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// Definir la URL base completa
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . BASE_PATH);

/**
 * Función helper para generar URLs correctas
 * 
 * @param string $path Ruta relativa desde la raíz del proyecto
 * @return string URL completa
 */
function url($path = '') {
    // Remover slash inicial si existe para evitar doble slash
    $path = ltrim($path, '/');
    return BASE_PATH . $path;
}

/**
 * Función para obtener rutas absolutas del sistema de archivos
 * 
 * @param string $path Ruta relativa
 * @return string Ruta absoluta del sistema
 */
function path($path = '') {
    return $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . ltrim($path, '/');
}

/**
 * Función para obtener la URL base completa (con protocolo y dominio)
 * 
 * @param string $path Ruta relativa
 * @return string URL absoluta completa
 */
function fullUrl($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}
?>