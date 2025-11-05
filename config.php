<?php
session_start();

// Funciones para archivos
function leerJSON($archivo) {
    if (file_exists($archivo)) {
        $contenido = file_get_contents($archivo);
        return json_decode($contenido, true);
    }
    return [];
}

function guardarJSON($archivo, $datos) {
    $json = json_encode($datos, JSON_PRETTY_PRINT);
    file_put_contents($archivo, $json);
}

function leerTXT($archivo) {
    if (file_exists($archivo)) {
        return file_get_contents($archivo);
    }
    return "";
}

function escribirTXT($archivo, $contenido) {
    file_put_contents($archivo, $contenido . PHP_EOL, FILE_APPEND);
}

function logAccion($mensaje) {
    $fecha = date('Y-m-d H:i:s');
    $log = "[$fecha] $mensaje" . PHP_EOL;
    file_put_contents('logs/errores.log', $log, FILE_APPEND);
}

// FUNCIÓN PARA MOSTRAR HEADER
function mostrarHeader($titulo_pagina = "Crew de Streamers") {
    // Si el usuario no está logueado, no mostrar header
    if (!isset($_SESSION['username_gamer'])) {
        return;
    }
    
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($titulo_pagina) . '</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="dark-theme">
    
    <header class="gaming-header">
        <h1>🎮 Crew Manager</h1>
        <div class="user-info">
            <span>Bienvenido, ' . htmlspecialchars($_SESSION['username_gamer']) . '</span>
            <span>Nivel: ' . htmlspecialchars($_SESSION['nivel_usuario']) . '</span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <nav class="gaming-nav">
        <a href="index.php">🏠 Home</a>
        <a href="desafio1.php">🎯 Desafío 1 - Chat Rápido</a>
        <a href="desafio2.php">🔄 Desafío 2 - Featured Streamers</a>
        <a href="desafio3.php">⚔️ Desafío 3 - Torneos</a>
        <a href="desafio4.php">🏆 Desafío 4 - Rankings</a>
        <a href="desafio5.php">🤝 Desafío 5 - Sponsors</a>
    </nav>
    ';
}

// FUNCIÓN PARA MOSTRAR FOOTER
function mostrarFooter() {
    // Si el usuario no está logueado, no mostrar footer
    if (!isset($_SESSION['username_gamer'])) {
        return;
    }
    
    echo '
    <footer class="gaming-footer">
        <p>Stats de sesión: Nivel ' . htmlspecialchars($_SESSION['nivel_usuario']) . ' | 
           Desafíos completados: ' . count($_SESSION['desafios_completados']) . '</p>
    </footer>
    </body>
    </html>
    ';
}

// Iniciar sesión si es nuevo usuario
if (!isset($_SESSION['username_gamer'])) {
    $_SESSION['nivel_usuario'] = 1;
    $_SESSION['desafios_completados'] = [];
    $_SESSION['timestamp_inicio'] = time();
}

// Manejar cookies de racha de días
if (isset($_COOKIE['ultima_visita'])) {
    $ultima_visita = $_COOKIE['ultima_visita'];
    $hoy = date('Y-m-d');
    
    if ($ultima_visita != $hoy) {
        // Nuevo día
        $racha_actual = isset($_COOKIE['racha_dias']) ? $_COOKIE['racha_dias'] : 0;
        setcookie('racha_dias', $racha_actual + 1, time() + (30 * 24 * 60 * 60));
    }
} else {
    // Primera visita
    setcookie('racha_dias', 1, time() + (30 * 24 * 60 * 60));
}

setcookie('ultima_visita', date('Y-m-d'), time() + (30 * 24 * 60 * 60));

// Contador de visitas
$contador_archivo = 'data/visitas.txt';
$visitas = file_exists($contador_archivo) ? (int)file_get_contents($contador_archivo) : 0;
$visitas++;
file_put_contents($contador_archivo, $visitas);
?>