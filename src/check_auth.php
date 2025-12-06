<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_name('TOLLAN_SESSION');
    session_start();
}

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/config.php';
}

function verificarAutenticacion() {
    if (!isset($_SESSION['ID_Usuarios'])) {
        $paginaActual = $_SERVER['PHP_SELF'];
        header("Location: " . url('src/login.php') . "?redirect=" . urlencode($paginaActual));
        exit;
    }
}

function verificarPermisos($rolesPermitidos = ['Adm', 'Mod', 'Usr']) {
    verificarAutenticacion();
    
    if (!in_array($_SESSION['Tipo_Usr'], $rolesPermitidos)) {
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

function getNombreUsuario() {
    return isset($_SESSION['Nombre']) ? $_SESSION['Nombre'] : 'Invitado';
}

function getTipoUsuario() {
    return isset($_SESSION['Tipo_Usr']) ? $_SESSION['Tipo_Usr'] : null;
}

function esAdmin() {
    return isset($_SESSION['Tipo_Usr']) && $_SESSION['Tipo_Usr'] === 'Adm';
}

function esModerador() {
    return isset($_SESSION['Tipo_Usr']) && 
           ($_SESSION['Tipo_Usr'] === 'Mod' || $_SESSION['Tipo_Usr'] === 'Adm');
}