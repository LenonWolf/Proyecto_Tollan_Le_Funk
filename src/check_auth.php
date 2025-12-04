<?php
// Archivo para verificar autenticación y permisos
// Incluir este archivo al inicio de cada página protegida

session_start();

/**
 * Verificar si el usuario está autenticado
 * Si no lo está, redirigir al login con la página de retorno
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['ID_Usuarios'])) {
        // Guardar la página actual para redirigir después del login
        $paginaActual = $_SERVER['PHP_SELF'];
        header("Location: /Proyecto_Tollan_Le_Funk/src/login.php?redirect=" . urlencode($paginaActual));
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
                header("Location: /Proyecto_Tollan_Le_Funk/ver_partida.php");
                break;
            default:
                header("Location: /Proyecto_Tollan_Le_Funk/index.php");
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