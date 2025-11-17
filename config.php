<?php
session_start();

/////////////////////////////////////////////////////////////////////////////////////////
// Procesar el logout si se solicita
/////////////////////////////////////////////////////////////////////////////////////////
if(isset($_GET['logout']) && $_GET['logout'] === 'true') {
    logout(); //Llama a la función de logout y hace exit
}

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
        $_SESSION['nivel_usuario'] = 0;
        $_SESSION['desafios_completados'] = array();
        $_SESSION['timestamp_inicio'] = time();
    } else {
        // Asegurar que nivel_usuario existe incluso para usuarios ya logueados
        if (!isset($_SESSION['nivel_usuario'])) {
            $_SESSION['nivel_usuario'] = 0;
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
    // Si no hay usuario logueado, no hacer nada
    if(!isset($_SESSION['username_gamer'])) {
        return 1;
    }
    
    $username = $_SESSION['username_gamer'];
    $hoy = date('Y-m-d');
    
    // Nombres de cookies únicos por usuario
    $cookie_racha = 'racha_dias_' . $username;
    $cookie_visita = 'ultima_visita_' . $username;
    
    // PRIMERA VEZ - No existe la cookie de última visita para este usuario
    if(!isset($_COOKIE[$cookie_visita])) {
        setcookie($cookie_racha, 1, time() + (30 * 24 * 60 * 60), '/');
        setcookie($cookie_visita, $hoy, time() + (30 * 24 * 60 * 60), '/');
        return 1;
    }
    
    $ultima_visita = $_COOKIE[$cookie_visita];
    $racha_actual = isset($_COOKIE[$cookie_racha]) ? (int)$_COOKIE[$cookie_racha] : 1;
    
    // SI YA VISITÓ HOY - Mantener racha
    if ($ultima_visita === $hoy) {
        return $racha_actual;
    }
    
    // CALCULAR SI VISITÓ AYER
    $ayer = date('Y-m-d', strtotime('-1 day'));
    
    if ($ultima_visita === $ayer) {
        // Visitó ayer - incrementar racha
        $nueva_racha = $racha_actual + 1;
        setcookie($cookie_racha, $nueva_racha, time() + (30 * 24 * 60 * 60), '/');
        setcookie($cookie_visita, $hoy, time() + (30 * 24 * 60 * 60), '/');
        return $nueva_racha;
    } else {
        // Saltó días - reiniciar a 1
        setcookie($cookie_racha, 1, time() + (30 * 24 * 60 * 60), '/');
        setcookie($cookie_visita, $hoy, time() + (30 * 24 * 60 * 60), '/');
        return 1;
    }
}
/////////////////////////////////////////////////////////////////////////////////////////
// FUNCIÓN PARA RESETEAR RACHA (PARA USUARIOS NUEVOS)
/////////////////////////////////////////////////////////////////////////////////////////

function resetearRachaUsuario() {
    if(!isset($_SESSION['username_gamer'])) {
        return 1;
    }
    
    $username = $_SESSION['username_gamer'];
    $hoy = date('Y-m-d');
    
    // Nombres de cookies únicos por usuario
    $cookie_racha = 'racha_dias_' . $username;
    $cookie_visita = 'ultima_visita_' . $username;
    
    // Eliminar cookies existentes de este usuario
    setcookie($cookie_racha, '', time() - 3600, '/');
    setcookie($cookie_visita, '', time() - 3600, '/');
    
    // Crear cookies nuevas para usuario nuevo
    setcookie($cookie_racha, 1, time() + (30 * 24 * 60 * 60), '/');
    setcookie($cookie_visita, $hoy, time() + (30 * 24 * 60 * 60), '/');
    
    return 1;
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
    // Si no hay usuario logueado, devolver 1
    if(!isset($_SESSION['username_gamer'])) {
        return 1;
    }
    
    $username = $_SESSION['username_gamer'];
    $cookie_racha = 'racha_dias_' . $username;
    
    // Verificar si tenemos cookie para este usuario
    if(isset($_COOKIE[$cookie_racha])) {
        return (int)$_COOKIE[$cookie_racha];
    } else {
        // Si no hay cookie, devolver 1 (primera vez)
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
        return 0;
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
        
        // NO eliminar las cookies de racha al hacer logout
        // Así se mantiene la racha entre sesiones
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

function formularioDesafio2($mensaje, $streamers) {
    // Asegurarnos que $streamers es un array
    if (!is_array($streamers)) {
        $streamers = array();
    }
    
    $html = <<<HTML
        <div class="info-section">
            <h2>📋 Instrucciones</h2>
            <p>Tu plataforma tiene una sección "Featured" que rota streamers cada día:</p>
            <ul>
                <li>El streamer destacado del día anterior se retira (eliminar el primer elemento)</li>
                <li>Llega un nuevo streamer invitado</li>
                <li>El orden se guarda en archivo JSON para persistencia</li>
            </ul>
        </div>
    HTML;

    if ($mensaje) {
        $clase = strpos($mensaje, '✅') !== false ? 'success' : 'error';
        $html .= <<<HTML
            <div class="$clase">$mensaje</div>
        HTML;
    }

    $html .= <<<HTML
        <div class="actions-section">
            <form method="POST" class="action-form">
                <button type="submit" name="rotar" class="btn-action">🔄 Rotar Featured Streamers</button>
            </form>
            
            <form method="POST" class="action-form">
                <button type="submit" name="reset" class="btn-reset">🔄 Reset Featured</button>
            </form>
        </div>

        <div class="streamers-section">
            <h2>🎬 Streamers Destacados ({$GLOBALS['numero_streamers']})</h2>
            <div class="avatars-container">
    HTML;

    // Solo mostrar si hay streamers
    if (!empty($streamers)) {
        foreach ($streamers as $index => $streamer) {
            $index_mas_uno = $index + 1;
            $html .= <<<HTML
                <div class="streamer-card">
                    <img src="$streamer" alt="Streamer $index_mas_uno" class="avatar">
                    <small>Posición $index_mas_uno</small>
                </div>
            HTML;
        }
    } else {
        $html .= '<p>No hay streamers disponibles</p>';
    }

    $html .= <<<HTML
            </div>
        </div>
    HTML;

    echo $html;
}

//Mostrar la seccion de bienvenida:
function mostrarSeccionBienvenida($username, $visitas, $desafios_completados) {
    // Obtener racha actual
    $racha_actual = obtenerRacha();
    
    echo '
    <section class="welcome-section">
        <h2>¡Bienvenido de nuevo, ' . htmlspecialchars($username) . '! 👋</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🔥 Racha Actual</h3>
                <p class="stat-number">' . $racha_actual . ' días</p>
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

    // Lista de nombres reales de streamers populares
    $nombres_streamers = [
        'Ibai', 'Auronplay', 'TheGrefg', 'Rubius', 'Illojuan',
        'xQc', 'Shroud', 'Ninja', 'Pokimane', 'Amouranth',
        'Kai Cenat', 'Adin Ross', 'SypherPK', 'Tfue', 'Myth',
        'TimTheTatman', 'DrDisrespect', 'Ludwig', 'Mizkif', 'Asmongold',
        'Quackity', 'Juja', 'Vegetta', 'Willyrex', 'Lolito',
        'Alexelcapo', 'ByViruzz', 'Reborn', 'Komanche', 'Zeling'
    ];

    echo '<section class="streamers-section">
        <h3>🎮 Tu Crew de Streamers:</h3>
        <div class="streamers-grid">';

    for($i = 0; $i < $mostrar; $i++){
        if (isset($nombres_streamers[$i])) {
            $nombre = $nombres_streamers[$i];
        } else {
            $nombre = 'Streamer ' . ($i + 1);
        }
        
        echo '<div class="avatar-card">';
        echo '<img src="' . $avatares[$i] . '" alt="' . $nombre . '">';
        echo '<span>' . $nombre . '</span>';
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

//Mostrar formulario de registro
function mostrarFormularioRegistro($error = '') {
    // Valores por defecto para los campos
    $username_value = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $email_value = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro - Crew de Streamers</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="dark-theme">
        <div class="login-container">
            <h1>🎮 Crear Cuenta</h1>';
            
    if ($error) {
        echo '<div class="error">' . $error . '</div>';
    }
    
    echo '
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           pattern="[a-zA-Z0-9_-]{3,20}" 
                           title="3-20 caracteres, solo letras, números, - y _"
                           value="' . $username_value . '">
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required
                           value="' . $email_value . '">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required
                           minlength="4"
                           title="Mínimo 4 caracteres">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Repetir Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="registro" class="btn-neon">🚀 Registrarse</button>
                
                <div class="login-links">
                    <p>¿Ya tienes cuenta? <a href="index.php">Iniciar Sesión</a></p>
                </div>
            </form>
        </div>
    </body>
    </html>';
    exit;
}

function mostrarFormularioLogin($error = '') {
    // Ver si hay usuario recordado
    $usuario_recordado = '';
    if (isset($_COOKIE['usuario_recordado'])) {
        $usuario_recordado = htmlspecialchars($_COOKIE['usuario_recordado']);
    }
    
    $username_value = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : $usuario_recordado;
    
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Crew de Streamers</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="dark-theme">
        <div class="login-container">
            <h1>🎮 Iniciar Sesión</h1>';
            
    if ($error) {
        echo '<div class="error">' . $error . '</div>';
    }
    
    echo '
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="' . $username_value . '">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember" name="remember" value="1" checked>
                    <label for="remember">💾 Recordar username</label>
                </div>
                
                <button type="submit" name="login" class="btn-neon">🎯 Entrar</button>
                
                <div class="login-links">
                    <p>¿No tienes cuenta? <a href="index.php?registro=true">Regístrate aquí</a></p>
                </div>
            </form>
        </div>
    </body>
    </html>';
    exit;
}

function guardarUsuarioEnArchivo($username, $email, $password) {
    // Crear carpeta data si no existe
    if (!is_dir('data')) {
        mkdir('data', 0777, true);
    }
    
    $archivo_usuarios = 'data/usuarios.txt';
    
    // Verificar si el usuario ya existe (MEJORADO)
    if (file_exists($archivo_usuarios)) {
        $lineas = file($archivo_usuarios, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lineas as $linea) {
            $datos = explode('|', $linea);
            if (isset($datos[0]) && $datos[0] === $username) {
                return false; // Usuario ya existe
            }
        }
    }
    
    // Guardar usuario en formato simple: username|email|password
    $linea = $username . '|' . $email . '|' . $password . PHP_EOL;
    
    // Usar FILE_APPEND para añadir al final
    if (file_put_contents($archivo_usuarios, $linea, FILE_APPEND | LOCK_EX) !== false) {
        return true;
    }
    
    return false;
}

function verificarUsuario($username, $password) {
    $archivo_usuarios = 'data/usuarios.txt';
    
    if (!file_exists($archivo_usuarios)) {
        return false;
    }
    
    $lineas = file($archivo_usuarios, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lineas as $linea) {
        $datos = explode('|', $linea);
        
        // Verificar que tenemos exactamente 3 partes
        if (count($datos) === 3) {
            $user = trim($datos[0]);
            $email = trim($datos[1]);
            $pass = trim($datos[2]);
            
            // Verificar usuario y contraseña (sin hash)
            if ($user === $username && $pass === $password) {
                return array(
                    'username' => $user,
                    'email' => $email
                );
            }
        }
    }
    
    return false;
}

function procesarRegistro() {
    $error = '';
    
    // Obtener datos del formulario
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // VALIDACIONES BÁSICAS
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    }
    elseif (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username)) {
        $error = "El username debe tener 3-20 caracteres y solo letras, números, - y _";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no tiene formato válido";
    }
    elseif (strlen($password) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres";
    }
    elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    }
    
    // Si hay error, mostrar formulario con error
    if ($error) {
        mostrarFormularioRegistro($error);
    }
    
    // Intentar guardar usuario
    if (guardarUsuarioEnArchivo($username, $email, $password)) {
        // ✅ RESETEAR RACHA PARA USUARIO NUEVO
        resetearRachaUsuario();
        
        // Registro exitoso - crear sesión
        $_SESSION['username_gamer'] = $username;
        $_SESSION['email_usuario'] = $email;
        $_SESSION['nivel_usuario'] = 0;
        $_SESSION['desafios_completados'] = array();
        $_SESSION['timestamp_inicio'] = time();
        
        // GUARDAR PROGRESO INICIAL
        guardarProgresoUsuario($username, 0, array());
        
        // Redirigir al dashboard
        header('Location: index.php');
        exit;
        
    } else {
        $error = "El username '$username' ya existe. Elige otro.";
        mostrarFormularioRegistro($error);
    }
}

//Función para procesar login

function procesarLogin() {
    $error = '';
    
    // Obtener datos del formulario
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Validaciones básicas
    if (empty($username) || empty($password)) {
        $error = "Username y contraseña son obligatorios";
        mostrarFormularioLogin($error);
    }
    
    // Verificar usuario
    $usuario = verificarUsuario($username, $password);
    
    if ($usuario) {
        // CARGAR PROGRESO DEL USUARIO
        $progreso = cargarProgresoUsuario($username);
        
        // Login exitoso - crear sesión CON PROGRESO GUARDADO
        $_SESSION['username_gamer'] = $usuario['username'];
        $_SESSION['email_usuario'] = $usuario['email'];
        $_SESSION['nivel_usuario'] = $progreso['nivel'];
        $_SESSION['desafios_completados'] = $progreso['desafios'];
        $_SESSION['timestamp_inicio'] = time();
        
        // ✅ FORZAR ACTUALIZACIÓN DE RACHA DESPUÉS DEL LOGIN
        $racha_actual = gestionarRacha();
        
        // Guardar cookie de "recordar username" si se marcó
        if ($remember) {
            setcookie('usuario_recordado', $username, time() + (30 * 24 * 60 * 60), '/');
        } else {
            // Si no marcó recordar, borrar la cookie si existe
            setcookie('usuario_recordado', '', time() - 3600, '/');
        }
        
        // Redirigir al dashboard
        header('Location: index.php');
        exit;
        
    } else {
        $error = "Username o contraseña incorrectos";
        mostrarFormularioLogin($error);
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA GUARDAR PROGRESO DE USUARIO  
/////////////////////////////////////////////////////////////////////////////////////////


function guardarProgresoUsuario($username, $nivel, $desafios_completados) {
    $archivo_progreso = 'data/progreso_usuarios.txt';

    if(!is_dir('data')) {
        mkdir('data', 0777, true);
    }

    //Leer progreso existente
    $progreso = array();
    if(file_exists($archivo_progreso)) {
        $lineas = file($archivo_progreso, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lineas as $linea) {
            $datos = explode('|', $linea);
            if(count($datos) >= 3) {
                $progreso[$datos[0]] = array (
                    'nivel' => $datos[1],
                    'desafios' => $datos[2]
                );
            }
        }
    }

    //Actualizar progreso del usuario
    $progreso[$username] = array(
        'nivel' => $nivel,
        'desafios' => implode(',', $desafios_completados)
    );

    //Guardar todo el progreso
    $contenido = '';
    foreach($progreso as $user => $datos) {
        $contenido .= $user . '|' . $datos['nivel'] . '|' . $datos['desafios'] . PHP_EOL;
    }

    file_put_contents($archivo_progreso, $contenido);

}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA CARGAR PROGRESO DE USUARIO  
/////////////////////////////////////////////////////////////////////////////////////////

function cargarProgresoUsuario($username) {
    $archivo_progreso = 'data/progreso_usuarios.txt';

    if(!file_exists($archivo_progreso)) {
        return array('nivel' => 0, 'desafios' => array());
    }

    $lineas = file($archivo_progreso, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach($lineas as $linea) {
        $datos = explode('|', $linea);
        if(count($datos) >= 3 && $datos[0] === $username) {
            $desafios_array = !empty($datos[2]) ? explode (',', $datos[2]) : array();
            return array(
                'nivel' => intval($datos[1]),
                'desafios' => $desafios_array
            );
        }
    }

    //Si no encuentra progreso, devolver valores por defecto
    return array('nivel' => 0, 'desafios' => array());
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA ACTUALIZAR PROGRESO DE USUARIO  
/////////////////////////////////////////////////////////////////////////////////////////
function actualizarProgresoUsuario() {
    if(isset($_SESSION['username_gamer'])) {
        $username = $_SESSION['username_gamer'];
        $nivel = $_SESSION['nivel_usuario'];
        $desafios = $_SESSION['desafios_completados'];

        guardarProgresoUsuario($username, $nivel, $desafios);
    }
}


/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA DESAFIO 3
/////////////////////////////////////////////////////////////////////////////////////////

function generarRosterStreamers() {
       $juegos = ["Fortnite", "Valorant", "Minecraft", "LOL", "Among Us", "Fall Guys", "Rocket League"];
    $nombres_reales = [
        "Alex Martínez", "Sara García", "Carlos López", "Marta Rodríguez", "David Fernández",
        "Laura González", "Javier Pérez", "Elena Sánchez", "Daniel Romero", "Ana Torres"
    ];
    
    $usernames = [
        "TheGamer2024", "ProPlayer99", "EpicStreamer", "GameMaster", "EliteWarrior",
        "ShadowHunter", "NeonBlade", "CyberNinja", "PixelKing", "StreamQueen"
    ];
    
    // Obtener imágenes reales que existen
    $avatares = glob('imagenes/streamers/*.{png,jpg,jpeg,gif}', GLOB_BRACE);
    
    $roster = array();
    
    for ($i = 0; $i < 10; $i++) {
    if (isset($avatares[$i])) {
        $avatar = basename($avatares[$i]);
    } else {
        $avatar = 'default_avatar.jpg';
    }
    
    $streamer = array(
        'username' => $usernames[$i],
        'nombre_real' => $nombres_reales[$i],
        'followers' => rand(5000, 100000),
        'avatar' => $avatar,
        'juego_favorito' => $juegos[array_rand($juegos)]
    );
    $roster[] = $streamer;
}
    
    return $roster;
}

function guardarRoster($roster) {
    $archivo = 'data/roster_completo.json';
    guardarJSON($archivo, $roster);
}

function cargarRoster() {
    $archivo = 'data/roster_completo.json';
    return leerJSON($archivo);
}

function dividirEquipos($roster) {
    $teamChaos = array();
    $teamOrder = array();
    
    foreach ($roster as $index => $streamer) {
        if ($index % 2 == 0) {
            $teamChaos[] = $streamer;
        } else {
            $teamOrder[] = $streamer;
        }
    }
    
    return array('chaos' => $teamChaos, 'order' => $teamOrder);
}

function calcularTotalFollowers($equipo) {
    $total = 0;
    foreach ($equipo as $streamer) {
        $total += $streamer['followers'];
    }
    return $total;
}

function encontrarMVP($roster) {
    $max_followers = 0;
    $mvp = array();
    
    foreach ($roster as $streamer) {
        if ($streamer['followers'] > $max_followers) {
            $max_followers = $streamer['followers'];
            $mvp = array($streamer);
        } elseif ($streamer['followers'] == $max_followers) {
            $mvp[] = $streamer;
        }
    }
    
    return $mvp;
}

function encontrarRookie($roster) {
    $min_followers = PHP_INT_MAX;
    $rookie = array();
    
    foreach ($roster as $streamer) {
        if ($streamer['followers'] < $min_followers) {
            $min_followers = $streamer['followers'];
            $rookie = array($streamer);
        } elseif ($streamer['followers'] == $min_followers) {
            $rookie[] = $streamer;
        }
    }
    
    return $rookie;
}

function mostrarEquiposTorneo($teamChaos, $teamOrder, $totalChaos, $totalOrder, $mvp, $rookie) {
    echo '
    <div class="torneo-header">
        <div class="equipo-info chaos">
            <h3>🔴 Team Chaos</h3>
            <p class="total-followers">' . number_format($totalChaos) . ' followers</p>
        </div>
        <div class="vs-badge">VS</div>
        <div class="equipo-info order">
            <h3>🔵 Team Order</h3>
            <p class="total-followers">' . number_format($totalOrder) . ' followers</p>
        </div>
    </div>';
    
    echo '<div class="equipos-container">';
    
    // Team Chaos
    echo '<div class="equipo equipo-chaos">';
    echo '<h4>🔴 Team Chaos</h4>';
    echo '<div class="streamers-equipo">';
    foreach ($teamChaos as $streamer) {
        echo '
        <div class="streamer-card">
            <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
            <div class="streamer-info">
                <h5>' . $streamer['username'] . '</h5>
                <p class="nombre-real">' . $streamer['nombre_real'] . '</p>
                <p class="followers">' . number_format($streamer['followers']) . ' followers</p>
                <p class="juego">🎮 ' . $streamer['juego_favorito'] . '</p>
            </div>
        </div>';
    }
    echo '</div></div>';
    
    // Team Order
    echo '<div class="equipo equipo-order">';
    echo '<h4>🔵 Team Order</h4>';
    echo '<div class="streamers-equipo">';
    foreach ($teamOrder as $streamer) {
        echo '
        <div class="streamer-card">
            <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
            <div class="streamer-info">
                <h5>' . $streamer['username'] . '</h5>
                <p class="nombre-real">' . $streamer['nombre_real'] . '</p>
                <p class="followers">' . number_format($streamer['followers']) . ' followers</p>
                <p class="juego">🎮 ' . $streamer['juego_favorito'] . '</p>
            </div>
        </div>';
    }
    echo '</div></div>';
    
    echo '</div>';
    
    // MVP y Rookie
    echo '<div class="premios-torneo">';
    
    // MVP
    echo '<div class="premio mvp">';
    echo '<h4>🏆 MVP del Torneo</h4>';
    foreach ($mvp as $streamer) {
        echo '
        <div class="premiado">
            <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
            <div>
                <h5>' . $streamer['username'] . '</h5>
                <p>' . number_format($streamer['followers']) . ' followers</p>
            </div>
        </div>';
    }
    echo '</div>';
    
    // Rookie
    echo '<div class="premio rookie">';
    echo '<h4>⭐ Rookie del Torneo</h4>';
    foreach ($rookie as $streamer) {
        echo '
        <div class="premiado">
            <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
            <div>
                <h5>' . $streamer['username'] . '</h5>
                <p>' . number_format($streamer['followers']) . ' followers</p>
            </div>
        </div>';
    }
    echo '</div>';
    
    echo '</div>';
}

function formularioTorneos($error, $resultado) {
    echo '
    <div class="form-section">
        <form method="POST">
            <div class="form-actions">
                <button type="submit" name="generar_roster" class="btn-neon">🎲 Generar Nuevo Roster</button>
            </div>
        </form>
    </div>';
    
    if ($error) {
        echo '<div class="error">' . $error . '</div>';
    }
    
    if ($resultado) {
        echo '<div class="success">' . $resultado . '</div>';
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// FUNCIONES PARA DESAFÍO 4 - RANKINGS
/////////////////////////////////////////////////////////////////////////////////////////

function generarRankingFollowers($roster) {
    // Ordenar por followers (descendente)
    $ranking = $roster;
    usort($ranking, function($a, $b) {
        return $b['followers'] - $a['followers'];
    });
    return $ranking;
}

function generarRankingAlfabetico($roster) {
    // Ordenar alfabéticamente por username
    $ranking = $roster;
    usort($ranking, function($a, $b) {
        return strcmp($a['username'], $b['username']);
    });
    return $ranking;
}

function buscarStreamer($roster, $termino) {
    $termino = strtolower(trim($termino));
    
    // Buscar streamer legendario primero
    if ($termino == 'legendkiller2024') {
        return array(
            'username' => 'LegendKiller2024',
            'nombre_real' => 'Alex Rodríguez',
            'followers' => 150000,
            'avatar' => 'legend.jpg',
            'juego_favorito' => 'Valorant',
            'es_legendario' => true
        );
    }
    
    // Buscar en el roster normal
    foreach ($roster as $streamer) {
        if (strtolower($streamer['username']) == $termino) {
            $streamer['encontrado'] = true;
            return $streamer;
        }
    }
    
    return null;
}

function logBusqueda($termino, $encontrado) {
    $fecha = date('Y-m-d H:i:s');
    $resultado = $encontrado ? 'ENCONTRADO' : 'NO_ENCONTRADO';
    $mensaje = "[$fecha] Búsqueda: '$termino' - $resultado";
    escribirTXT('logs/busquedas.txt', $mensaje);
}

function mostrarRankingFollowers($ranking) {
    echo '<div class="ranking-podio">';
    echo '<h4>🔥 Top Followers</h4>';
    
    // Podio para los top 3
    if (count($ranking) >= 3) {
        echo '<div class="podio-top3">';
        
        // Segundo lugar
        echo '<div class="podio-item segundo">';
        echo '<div class="medalla">🥈</div>';
        echo '<img src="imagenes/streamers/' . $ranking[1]['avatar'] . '" alt="' . $ranking[1]['username'] . '">';
        echo '<h5>' . $ranking[1]['username'] . '</h5>';
        echo '<p>' . number_format($ranking[1]['followers']) . ' followers</p>';
        echo '</div>';
        
        // Primer lugar
        echo '<div class="podio-item primero">';
        echo '<div class="medalla">🥇</div>';
        echo '<img src="imagenes/streamers/' . $ranking[0]['avatar'] . '" alt="' . $ranking[0]['username'] . '">';
        echo '<h5>' . $ranking[0]['username'] . '</h5>';
        echo '<p>' . number_format($ranking[0]['followers']) . ' followers</p>';
        echo '</div>';
        
        // Tercer lugar
        echo '<div class="podio-item tercero">';
        echo '<div class="medalla">🥉</div>';
        echo '<img src="imagenes/streamers/' . $ranking[2]['avatar'] . '" alt="' . $ranking[2]['username'] . '">';
        echo '<h5>' . $ranking[2]['username'] . '</h5>';
        echo '<p>' . number_format($ranking[2]['followers']) . ' followers</p>';
        echo '</div>';
        
        echo '</div>';
    }
    
    // Resto del ranking
    echo '<div class="ranking-lista">';
    for ($i = 3; $i < count($ranking); $i++) {
        $posicion = $i + 1;
        echo '
        <div class="ranking-item">
            <span class="posicion">' . $posicion . 'º</span>
            <img src="imagenes/streamers/' . $ranking[$i]['avatar'] . '" alt="' . $ranking[$i]['username'] . '">
            <div class="info">
                <h5>' . $ranking[$i]['username'] . '</h5>
                <p>' . number_format($ranking[$i]['followers']) . ' followers</p>
            </div>
            <span class="juego">🎮 ' . $ranking[$i]['juego_favorito'] . '</span>
        </div>';
    }
    echo '</div>';
    echo '</div>';
}

function mostrarRankingAlfabetico($ranking) {
    echo '<div class="ranking-alfabetico">';
    echo '<h4>📋 Orden Alfabético</h4>';
    echo '<div class="lista-alfabetica">';
    
    foreach ($ranking as $streamer) {
        $letra = strtoupper(substr($streamer['username'], 0, 1));
        echo '
        <div class="item-alfabetico">
            <span class="letra-indicador">' . $letra . '</span>
            <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
            <div class="info">
                <h5>' . $streamer['username'] . '</h5>
                <p class="nombre-real">' . $streamer['nombre_real'] . '</p>
            </div>
            <span class="followers">' . number_format($streamer['followers']) . ' 🎯</span>
        </div>';
    }
    
    echo '</div>';
    echo '</div>';
}

function mostrarResultadoBusqueda($streamer, $termino) {
    if ($streamer) {
        echo '<div class="resultado-busqueda encontrado">';
        echo '<div class="success-header">';
        echo '✅ ¡Encontrado!';
        if (isset($streamer['es_legendario'])) {
            echo ' <span class="legend-badge">🌟 LEGENDARIO</span>';
        }
        echo '</div>';
        
        echo '<div class="streamer-encontrado">';
        echo '<img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">';
        echo '<div class="streamer-info">';
        echo '<h4>' . $streamer['username'] . '</h4>';
        echo '<p class="nombre-real">' . $streamer['nombre_real'] . '</p>';
        echo '<p class="followers">' . number_format($streamer['followers']) . ' followers</p>';
        echo '<p class="juego">🎮 ' . $streamer['juego_favorito'] . '</p>';
        if (isset($streamer['es_legendario'])) {
            echo '<p class="legend-desc">🌟 Streamer legendario con habilidades extraordinarias</p>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="resultado-busqueda no-encontrado">';
        echo '<div class="error-header">❌ No existe ese username en tu crew</div>';
        echo '<p>Intenta con otro nombre o busca al legendario <strong>LegendKiller2024</strong></p>';
        echo '</div>';
    }
}

function formularioRankings($error, $termino_busqueda = '') {
    echo '
    <div class="search-section">
        <h3>🔍 Buscador de Legends</h3>
        <form method="POST" class="search-form">
            <div class="search-container">
                <input type="text" name="busqueda" placeholder="Buscar streamer por username..." 
                       value="' . htmlspecialchars($termino_busqueda) . '" required
                       pattern="[a-zA-Z0-9_-]{3,20}"
                       title="Solo letras, números, guiones y guiones bajos (3-20 caracteres)">
                <button type="submit" name="buscar" class="btn-search">🔍 Buscar</button>
            </div>
        </form>';
    
    if ($error) {
        echo '<div class="error">' . $error . '</div>';
    }
    
    echo '</div>';
}


/////////////////////////////////////////////////////////////////////////////////////////
// FUNCION PARA DESAFIO 5 
/////////////////////////////////////////////////////////////////////////////////////////

function obtenerJuegosFavoritos($roster) {
    $juegos = array();
    foreach ($roster as $streamer) {
        $juegos[] = $streamer['juego_favorito'];
    }
    
    // Eliminar duplicados y convertir a string
    $juegos_unicos = array_unique($juegos);
    $juegos_string = implode(' | ', $juegos_unicos);
    
    // Guardar en archivo
    escribirTXT('data/juegos_trending.txt', "🎮 Juegos más jugados: " . $juegos_string);
    
    return $juegos_string;
}

function cargarSponsors() {
    $archivo = 'data/sponsors.txt';
    
    // Si el archivo no existe, crear con sponsors por defecto
    if (!file_exists($archivo)) {
        $sponsors_default = "Red Bull Gaming;Logitech G;HyperX;Razer;NVIDIA;Corsair;SteelSeries";
        file_put_contents($archivo, $sponsors_default);
    }
    
    $contenido = file_get_contents($archivo);
    
    // Si el contenido está vacío, usar sponsors por defecto
    if (empty(trim($contenido))) {
        $contenido = "Red Bull Gaming;Logitech G;HyperX;Razer;NVIDIA;Corsair;SteelSeries";
        file_put_contents($archivo, $contenido);
    }
    
    $sponsors_array = explode(';', $contenido);
    
    // Limpiar espacios en blanco
    $sponsors_limpios = array();
    foreach ($sponsors_array as $sponsor) {
        $sponsor_limpio = trim($sponsor);
        if (!empty($sponsor_limpio)) {
            $sponsors_limpios[] = $sponsor_limpio;
        }
    }
    
    // Si después de limpiar está vacío, usar sponsors por defecto
    if (empty($sponsors_limpios)) {
        $sponsors_limpios = array(
            "Red Bull Gaming", "Logitech G", "HyperX", "Razer", 
            "NVIDIA", "Corsair", "SteelSeries"
        );
        file_put_contents($archivo, implode(';', $sponsors_limpios));
    }
    
    return $sponsors_limpios;
}

function asignarSponsors($roster, $sponsors) {
    $roster_con_sponsors = array();
    
    // Asegurarnos de que hay sponsors disponibles
    if (empty($sponsors)) {
        $sponsors = array("Sin Sponsor");
    }
    
    foreach ($roster as $streamer) {
        $sponsor_aleatorio = $sponsors[array_rand($sponsors)];
        $streamer['sponsor'] = $sponsor_aleatorio;
        $roster_con_sponsors[] = $streamer;
    }
    
    return $roster_con_sponsors;
}

function guardarColaboracionesCSV($roster_con_sponsors) {
    $archivo = 'data/colaboraciones.csv';
    $contenido = "username,nombre_real,sponsor,followers,juego\n";
    
    foreach ($roster_con_sponsors as $streamer) {
        $linea = array(
            $streamer['username'],
            $streamer['nombre_real'],
            $streamer['sponsor'],
            $streamer['followers'],
            $streamer['juego_favorito']
        );
        $contenido .= implode(',', $linea) . "\n";
    }
    
    file_put_contents($archivo, $contenido);
}

function mostrarTablaColaboraciones($roster_con_sponsors) {
    echo '<div class="tabla-colaboraciones">';
    echo '<h4>💼 Colaboraciones Actuales</h4>';
    echo '<div class="table-container">';
    echo '<table class="sponsors-table">';
    echo '<thead>
            <tr>
                <th>STREAMER</th>
                <th>SPONSOR</th>
                <th>FOLLOWERS</th>
                <th>JUEGO FAVORITO</th>
            </tr>
          </thead>
          <tbody>';
    
    foreach ($roster_con_sponsors as $streamer) {
        echo '<tr>
                <td>
                    <div class="streamer-cell">
                        <img src="imagenes/streamers/' . $streamer['avatar'] . '" alt="' . $streamer['username'] . '">
                        <div>
                            <strong>' . $streamer['username'] . '</strong>
                            <br><small>' . $streamer['nombre_real'] . '</small>
                        </div>
                    </div>
                </td>
                <td><span class="sponsor-badge">' . $streamer['sponsor'] . '</span></td>
                <td class="followers-cell">' . number_format($streamer['followers']) . '</td>
                <td class="juego-cell">🎮 ' . $streamer['juego_favorito'] . '</td>
              </tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

function mostrarJuegosTrending($juegos_string) {
    echo '<div class="juegos-trending">';
    echo '<h4>🎮 Juegos más jugados</h4>';
    echo '<div class="juegos-lista">';
    echo '<p>' . $juegos_string . '</p>';
    echo '</div>';
    echo '</div>';
}

function mostrarListaSponsors($sponsors) {
    echo '<div class="lista-sponsors">';
    echo '<h4>🏢 Sponsors Disponibles</h4>';
    echo '<div class="sponsors-grid">';
    
    foreach ($sponsors as $sponsor) {
        echo '<div class="sponsor-item">' . $sponsor . '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}

function formularioSponsors($error, $success) {
    echo '
    <div class="form-sponsors">
        <h4>💼 Añadir nuevo sponsor al roster</h4>
        <form method="POST" class="sponsor-form">
            <div class="form-group">
                <input type="text" name="nuevo_sponsor" placeholder="Nombre del sponsor (ej: MSI Gaming)" 
                       required pattern="[a-zA-Z0-9\s]{3,50}"
                       title="Solo letras, números y espacios (3-50 caracteres)">
                <button type="submit" name="añadir_sponsor" class="btn-neon">➕ Añadir Sponsor</button>
            </div>
        </form>';
    
    if ($error) {
        echo '<div class="error">' . $error . '</div>';
    }
    
    if ($success) {
        echo '<div class="success">' . $success . '</div>';
    }
    
    echo '</div>';
}

function descargarReporteCSV() {
    $archivo = 'data/colaboraciones.csv';
    if (file_exists($archivo)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reporte_colaboraciones.csv"');
        readfile($archivo);
        exit;
    }
}