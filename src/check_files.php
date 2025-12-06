<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificación de Archivos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .ok { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
        h3 { border-bottom: 2px solid #333; }
    </style>
</head>
<body>
    <h1>Verificación de Archivos PHP</h1>
    
    <?php
    $files = array(
        'session_config.php',
        'config.php',
        'conexion.php',
        'login.php',
        'procesar_login.php'
    );
    
    foreach ($files as $file) {
        $path = __DIR__ . '/' . $file;
        
        echo "<h3>$file</h3>";
        
        if (!file_exists($path)) {
            echo "<p class='error'>❌ No existe</p>";
            continue;
        }
        
        $content = file_get_contents($path);
        $len = strlen($content);
        
        echo "<p>Tamaño: $len bytes</p>";
        
        // Ver primeros bytes
        $first = substr($content, 0, 10);
        $hex = '';
        for ($i = 0; $i < strlen($first); $i++) {
            $hex .= bin2hex($first[$i]) . ' ';
        }
        
        echo "<p>Primeros bytes (hex): <code>$hex</code></p>";
        
        // Verificar BOM
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            echo "<p class='error'>❌ Tiene BOM UTF-8</p>";
        } else {
            echo "<p class='ok'>✅ Sin BOM</p>";
        }
        
        // Ver inicio del archivo
        $preview = htmlspecialchars(substr($content, 0, 100));
        echo "<pre>$preview</pre>";
        
        echo "<hr>";
    }
    ?>
    
    <h2>Solución si hay BOM:</h2>
    <ol>
        <li>Descarga los archivos con problemas</li>
        <li>Ábrelos en VS Code o Notepad++</li>
        <li>Guárdalos como UTF-8 sin BOM</li>
        <li>Vuelve a subirlos</li>
    </ol>
</body>
</html>