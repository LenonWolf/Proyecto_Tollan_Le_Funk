<?php
/**
 * Archivo de configuración para rutas y entorno
 * 
 * Detecta automáticamente si está en localhost o en Azure
 * y ajusta las rutas correspondientemente
 */

// Detectar el entorno
$is_local = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') !== false
);

// Configurar las rutas base según el entorno
if ($is_local) {
    // Entorno local
    define('BASE_URL', '/Tollan_Le_Funk');
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Tollan_Le_Funk');
} else {
    // Entorno de producción (Azure)
    define('BASE_URL', '');
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);
}

// URL completa del sitio
define('SITE_URL', 
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
    . "://" . $_SERVER['HTTP_HOST'] . BASE_URL
);

/**
 * Función auxiliar para generar URLs
 * 
 * @param string $path Ruta relativa
 * @return string URL completa
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

/**
 * Función auxiliar para redirecciones
 * 
 * @param string $path Ruta relativa
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}
?>