<?php
$isAzure = (strpos($_SERVER['HTTP_HOST'], 'azurewebsites.net') !== false);
if ($isAzure) {
    define('BASE_PATH', '/');
} else {
    define('BASE_PATH', '/Tollan_Le_Funk/');
}
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . BASE_PATH);
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_PATH . $path;
}
function path($path = '') {
    return $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . ltrim($path, '/');
}
function fullUrl($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}