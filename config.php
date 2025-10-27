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