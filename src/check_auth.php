<?php
// Archivo para verificar autenticación y permisos
// Incluir este archivo al inicio de cada página protegida

// Configurar sesión usando archivo centralizado
require_once __DIR__ . '/session_config.php';

session_start();

// Cargar la configuración para usar la función url()
require_once __DIR__ . '/config.php';

/**
 * Verificar si el usuario está autenticado
 * Si no lo está, redirigir al login con la página de retorno
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['ID_Usuarios'])) {
        // Guardar la página actual para redirigir después del login
        $paginaActual = $_SERVER['PHP_SELF'];
        
        // Usar la función url() para generar la ruta correcta
        header("Location: " . url('src/login.php') . "?redirect=" . urlencode($paginaActual));
        exit;
    }
}

/**
 * Verificar si el usuario tiene los permisos necesarios
 * 
 * @param array $rolesPermitidos - Array de roles que pueden acceder ['Adm', 'Mod']
 */
function verificarPermisos($rolesPermitidos = ['Adm', 'Mod', 'Usr']) {
    // Primero verificar si está autenticado
    verificarAutenticacion();
    
    // Luego verificar si tiene el rol adecuado
    if (!in_array($_SESSION['Tipo_Usr'], $rolesPermitidos)) {
        // Si no tiene permisos, redirigir según su rol
        switch ($_SESSION['Tipo_Usr']) {
            case 'Usr':
                header("Location: " . url('ver_partida.php'));
                break;
            default:
                header("Location: " . url('index.php'));
        }
        exit;
    }
}

/**
 * Obtener el nombre del usuario actual
 */
function getNombreUsuario() {
    return isset($_SESSION['Nombre']) ? $_SESSION['Nombre'] : 'Invitado';
}

/**
 * Obtener el tipo de usuario actual
 */
function getTipoUsuario() {
    return isset($_SESSION['Tipo_Usr']) ? $_SESSION['Tipo_Usr'] : null;
}

/**
 * Verificar si el usuario es administrador
 */
function esAdmin() {
    return isset($_SESSION['Tipo_Usr']) && $_SESSION['Tipo_Usr'] === 'Adm';
}

/**
 * Verificar si el usuario es moderador o administrador
 */
function esModerador() {
    return isset($_SESSION['Tipo_Usr']) && 
           ($_SESSION['Tipo_Usr'] === 'Mod' || $_SESSION['Tipo_Usr'] === 'Adm');
}