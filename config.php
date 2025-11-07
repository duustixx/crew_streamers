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
    return array();
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
// FUNCION PARA INICIAR SESIÓN DE USUARIO
/////////////////////////////////////////////////////////////////////////////////////////

function iniciarSesionUsuario() {
    if (!isset($_SESSION['username_gamer'])) {
        $_SESSION['nivel_usuario'] = 1;
        $_SESSION['desafios_completados'] = array();
        $_SESSION['timestamp_inicio'] = time();
    } else {
        // Asegurar que nivel_usuario existe incluso para usuarios ya logueados
        if (!isset($_SESSION['nivel_usuario'])) {
            $_SESSION['nivel_usuario'] = 1;
        }
        if (!isset($_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'] = array();
        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA GESTIONAR RACHA
/////////////////////////////////////////////////////////////////////////////////////////

function gestionarRacha(){
    $hoy = date('Y-m-d');

    if(isset($_COOKIE['ultima_visita'])){
        $ultima_visita = $_COOKIE['ultima_visita'];

        // Si la ultima visita fue ayer la racha incrementara
        $ayer = date('Y-m-d', strtotime('-1 day'));
        if($ultima_visita == $ayer){
            if(isset($_COOKIE['racha_dias'])){
                $racha_actual = $_COOKIE['racha_dias'] + 1;
            } else {
                $racha_actual = 2;
            }
            setcookie('racha_dias', $racha_actual, time() + (30 * 24 * 60 * 60), '/');
        }
        // Si la utlima visita es hoy, mantenemos racha
        elseif ($ultima_visita == $hoy) {
            // No hacemos nada, mantenemos la racha
        } else {
            // Si hay mas de 1 dia de diferencia, la racha se reiniciara
            setcookie('racha_dias', 1, time() + (30 * 24 * 60 * 60), '/');
        }
    } else {
        // Primera Visita
        setcookie('racha_dias', 1, time() + (30 * 24 * 60 * 60), '/');
    }

    // Actualizamos la ultima visita
    setcookie('ultima_visita', $hoy, time() + (30 * 24 * 60 * 60), '/');

    // Devolvemos la racha actual
    if (isset($_COOKIE['racha_dias'])) {
        return $_COOKIE['racha_dias'];
    } else {
        return 1;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA OBTENER TEMA
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerTema(){
    if(isset($_COOKIE['tema'])){
        return $_COOKIE['tema'];
    } else {
        return 'dark';
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA CONTADOR DE VISITAS
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerContadorVisitas() {
    $contador_archivo = 'data/visitas.txt';
    if(file_exists($contador_archivo)){
        $visitas = (int)file_get_contents($contador_archivo);
    } else {
        $visitas = 0;
    }
    
    $visitas++;
    file_put_contents($contador_archivo, $visitas);
    
    return $visitas;
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA OBTENER DESAFIOS COMPLETADOS
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerDesafiosCompletados() {
    if (isset($_SESSION['desafios_completados'])) {
        return count($_SESSION['desafios_completados']);
    } else {
        return 0;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA OBTENER RACHA
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerRacha() {
    if(isset($_COOKIE['racha_dias'])){
        return $_COOKIE['racha_dias'];
    } else {
        return 1;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA OBTENER NIVEL USUARIO
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerNivelUsuario() {
    if (isset($_SESSION['nivel_usuario'])) {
        return $_SESSION['nivel_usuario'];
    } else {
        return 1;
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA OBTENER USERNAME
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerUsername() {
    if (isset($_SESSION['username_gamer'])) {
        return $_SESSION['username_gamer'];
    } else {
        return '';
    }
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

        $mensaje_log = "LOGOUT - Usuario: $username, Duración sesión: $minutos minutos, Nivel: " . obtenerNivelUsuario();
        logAccion($mensaje_log);
    }

    // Destruir todas las variables de sesión
    $_SESSION = array();

    // Si se desea destruir la sesión completamente, borrar también la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir la sesión
    session_destroy();

    // Redirigir al login
    header('Location: index.php');
    exit;
}

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para ESTRUCTURA o ESQUELETO del SITE
/////////////////////////////////////////////////////////////////////////////////////////

function mostrarHeader($titulo_pagina = "Crew de Streamers") {
    // Si el usuario no está logueado, no mostrar header
    if (!isset($_SESSION['username_gamer'])) {
        return;
    }

    $tema_actual = obtenerTema();
    $nivel_usuario = obtenerNivelUsuario();
    $username = obtenerUsername();
    
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($titulo_pagina) . '</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="' . $tema_actual . '-theme">
    
    <header class="gaming-header">
        <h1>🎮 Crew Manager</h1>
        <div class="user-info">
            <span>Bienvenido, ' . htmlspecialchars($username) . '</span>
            <span>Nivel: ' . htmlspecialchars($nivel_usuario) . '</span>
            <a href="?logout=true" class="btn-logout">Cerrar Sesión</a>
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

    $numero_desafios = obtenerDesafiosCompletados();
    $nivel_usuario = obtenerNivelUsuario();
    
    echo '
    <footer class="gaming-footer">
        <p>Stats de sesión: Nivel ' . htmlspecialchars($nivel_usuario) . ' | 
           Desafíos completados: ' . $numero_desafios . '</p>
    </footer>
    </body>
    </html>
    ';
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCIONES PARA INICIALIZAR LA APLICACIÓN
/////////////////////////////////////////////////////////////////////////////////////////

function inicializarAplicacion() {
    iniciarSesionUsuario();
    gestionarRacha();
    return obtenerContadorVisitas();
}

// Inicializar la aplicación
$visitas = inicializarAplicacion();

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para imprimir HTMLs (formularios...)
/////////////////////////////////////////////////////////////////////////////////////////

function formularioDesafio1($error, $resultado, $ganadores) {
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

    // Cierra el formulario
    $form .= <<<HTML
                <button type="submit">Iniciar Sorteo</button>
            </form>
        </div>
    HTML;                
        
    echo $form;

    if ($resultado) {
        $nivel_actual = obtenerNivelUsuario();
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
            <p class="success">🎉 ¡Desafío completado! Nivel subido a <?= $nivel_actual ?></p>
        </div>
        <?php
    }
}

//Mostrar la seccion de bienvenida:
function mostrarSeccionBienvenida($username, $racha, $visitas, $desafios_completados) {
    echo '
    <section class="welcome-section">
        <h2>¡Bienvenido de nuevo, ' . htmlspecialchars($username) . '! 👋</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🔥 Racha Actual</h3>
                <p class="stat-number">' . $racha . ' días</p>
            </div>
            <div class="stat-card">
                <h3>👥 Total Visitas</h3>
                <p class="stat-number">' . $visitas . '</p>
            </div>
            <div class="stat-card">
                <h3>🎯 Desafíos Completados</h3>
                <p class="stat-number">' . $desafios_completados . '/5</p>
            </div>
        </div>
    </section>';
}

//Mostrar Imagenes
function mostrarSeccionStreamers() {
    $avatares = glob('imagenes/streamers/*.{png,jpg,jpeg,gif}', GLOB_BRACE);
    $numero_avatares = count($avatares);
    $mostrar = 20;
    
    if($numero_avatares < 20){
        $mostrar = $numero_avatares;
    }

    echo '<section class="streamers-section">
        <h3>🎮 Tu Crew de Streamers:</h3>
        <div class="streamers-grid">';

    for($i = 0; $i < $mostrar; $i++){
        echo '<div class="avatar-card">';
        echo '<img src="' . $avatares[$i] . '" alt="Streamer' . ($i+1) . '">';
        echo '<span>Streamer ' . ($i+1) . '</span>';
        echo '</div>';
    }

    echo '</div>
    </section>';
}

//Mostrar formulario del login
function mostrarLogin(){
 echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido al Crew</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="dark-theme">
        <div class="login-container">
            <h1>🎮 Únete al Crew</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Elige tu username gamer:</label>
                    <input type="text" id="username" name="username" required 
                           pattern="[a-zA-Z0-9_-]{3,20}" 
                           title="Solo letras, números, guiones y guiones bajos (3-20 caracteres)">
                </div>
                <button type="submit" class="btn-neon">🚀 Entrar al Dashboard</button>
            </form>
        </div>
    </body>
    </html>';
    exit;
}