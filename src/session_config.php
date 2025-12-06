<?php
/**
 * Configuración centralizada de sesiones para Azure App Service
 * Incluir este archivo ANTES de session_start() en todas las páginas
 * 
 * IMPORTANTE: Este archivo debe incluirse antes de cualquier salida HTML
 */

// Solo configurar si la sesión no ha sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    
    // Detectar si estamos en Azure
    $isAzure = (strpos($_SERVER['HTTP_HOST'] ?? '', 'azurewebsites.net') !== false);
    
    if ($isAzure) {
        // Configuración específica para Azure
        
        // Crear directorio de sesiones si no existe
        $sessionPath = '/home/site/sessions';
        if (!is_dir($sessionPath)) {
            @mkdir($sessionPath, 0777, true);
        }
        
        // Configurar ruta de sesiones solo si el directorio existe y es escribible
        if (is_dir($sessionPath) && is_writable($sessionPath)) {
            @ini_set('session.save_path', $sessionPath);
        }
    }
    
    // Configuración general de sesiones (Azure y local)
    // Usar @ para suprimir warnings si las sesiones ya están iniciadas
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_samesite', 'Lax');
    @ini_set('session.gc_maxlifetime', '7200'); // 2 horas
    @ini_set('session.name', 'TOLLAN_SESSION');
    
    // Si estamos en HTTPS, activar cookies seguras
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        @ini_set('session.cookie_secure', '1');
    }
    
    // Configurar tiempo de vida de la cookie de sesión
    // 0 = hasta que se cierre el navegador
    @ini_set('session.cookie_lifetime', '0');
}
?>