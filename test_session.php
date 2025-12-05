<?php
/**
 * Archivo de diagnóstico para verificar sesiones
 * Accede a: https://tu-app.azurewebsites.net/test_session.php
 */

session_start();

// Si es la primera vez, guardar algo en sesión
if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = 'Sesión creada: ' . date('Y-m-d H:i:s');
    $_SESSION['contador'] = 1;
} else {
    $_SESSION['contador']++;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Sesiones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: green; }
        .error { color: red; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        pre {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Sesiones</h1>
    
    <div class="info">
        <h2>Estado de la Sesión</h2>
        <?php if (isset($_SESSION['test'])): ?>
            <p class="success">✅ La sesión está funcionando</p>
            <p><strong>Contador de recargas:</strong> <?php echo $_SESSION['contador']; ?></p>
            <p><strong>Mensaje guardado:</strong> <?php echo $_SESSION['test']; ?></p>
        <?php else: ?>
            <p class="error">❌ La sesión NO está funcionando</p>
        <?php endif; ?>
    </div>

    <div class="info">
        <h2>Configuración de Sesiones PHP</h2>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session Name:</strong> <?php echo session_name(); ?></p>
        <p><strong>Save Path:</strong> <?php echo session_save_path(); ?></p>
        <p><strong>Session Handler:</strong> <?php echo ini_get('session.save_handler'); ?></p>
        <p><strong>Cookie Lifetime:</strong> <?php echo ini_get('session.cookie_lifetime'); ?></p>
        <p><strong>Cookie Path:</strong> <?php echo ini_get('session.cookie_path'); ?></p>
        <p><strong>Cookie Domain:</strong> <?php echo ini_get('session.cookie_domain'); ?></p>
        <p><strong>Cookie Secure:</strong> <?php echo ini_get('session.cookie_secure') ? 'Yes' : 'No'; ?></p>
        <p><strong>Cookie HttpOnly:</strong> <?php echo ini_get('session.cookie_httponly') ? 'Yes' : 'No'; ?></p>
        <p><strong>Cookie SameSite:</strong> <?php echo ini_get('session.cookie_samesite'); ?></p>
    </div>

    <div class="info">
        <h2>Contenido Completo de $_SESSION</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>

    <div class="info">
        <h2>Información del Servidor</h2>
        <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p><strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
        <p><strong>Protocolo:</strong> <?php echo $_SERVER['SERVER_PROTOCOL']; ?></p>
        <p><strong>HTTPS:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'Yes' : 'No'; ?></p>
    </div>

    <div class="info">
        <h2>Acciones</h2>
        <p><a href="?">🔄 Recargar página (el contador debería aumentar)</a></p>
        <p><a href="?destroy=1">🗑️ Destruir sesión</a></p>
    </div>

    <?php
    // Opción para destruir sesión
    if (isset($_GET['destroy'])) {
        session_destroy();
        echo '<div class="info"><p class="success">Sesión destruida. <a href="test_session.php">Crear nueva sesión</a></p></div>';
    }
    ?>
</body>
</html>