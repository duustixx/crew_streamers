<?php
session_start();

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para archivos
/////////////////////////////////////////////////////////////////////////////////////////

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

/////////////////////////////////////////////////////////////////////////////////////////
// Funcion para LOGOUT
/////////////////////////////////////////////////////////////////////////////////////////

function logout() {
    // Registrar en log la acción de logout
    if(isset($_SESSION['username_gamer'])) {
        $username = $_SESSION['username_gamer'];
        $duracion_sesion = time() - $_SESSION['timestamp_inicio'];
        $minutos = floor($duracion_sesion / 60);

        $mensaje_log = "LOGOUT - Usuario: $username, Duración sesión: $minutos minutos, Nivel: {$_SESSION['nivel_usuario']}";
        logAccion($mensaje_log);
    }

    

}


/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para ESTRUCTURA o ESQUELETO del SITE
/////////////////////////////////////////////////////////////////////////////////////////

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


/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para imprimir HTMLs (formularios...)
/////////////////////////////////////////////////////////////////////////////////////////

function formularioDesafio1($error,$resultado,$ganadores) {

    $viewers_chat = isset($_SESSION['viewers_chat']) ? $_SESSION['viewers_chat'] : '';


    $form = <<<HTML
        <div class="form-section">
            <h2>Configuración del Sorteo</h2>
            
            <form method="POST">
                <label for="viewers">¿Cuántos viewers hay en el chat? (50-200)</label>
                <input type="number" id="viewers" name="viewers" 
                       value="{$viewers_chat}">
    HTML;       

    // Si hay error, añadir bloque de error
    if ($error) {
    $form .= <<<HTML
            <div class="error">$error</div>
    HTML;
    }

    //Cierra el formulario
    $form .= <<<HTML
                <button type="submit">Iniciar Sorteo</button>
            </form>
        </div>
    HTML;                
        
    echo $form;

if ($resultado) {
    ?>
    <div class="result-section">
        <h2><?= $resultado ?></h2>

        <?php if (isset($ganadores)) : ?>
            <div class="ganadores-grid">
                <?php foreach ($ganadores as $ganador) : ?>
                    <img src="<?= $ganador ?>" alt="Ganador" class="avatar">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="success">✅ Sorteo registrado en el log correctamente</p>
        <p class="success">🎉 ¡Desafío completado! Nivel subido a <?= $_SESSION['nivel_usuario'] ?></p>
    </div>
    <?php
}
}