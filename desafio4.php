<?php
require_once 'config.php';

// Verificar logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    logout();
}

// Si no está logueado, redirigir al login
if(!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$termino_busqueda = '';
$streamer_encontrado = null;
$mostrar_resultado = false;

// Cargar roster existente o generar uno si no existe
$roster = cargarRoster();
if (empty($roster)) {
    $roster = generarRosterStreamers();
    guardarRoster($roster);
}

// Generar rankings
$ranking_followers = generarRankingFollowers($roster);
$ranking_alfabetico = generarRankingAlfabetico($roster);

// Procesar búsqueda
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar'])) {
    $termino_busqueda = trim($_POST['busqueda']);
    
    // Validaciones
    if (empty($termino_busqueda)) {
        $error = 'Debes ingresar un username para buscar';
    } elseif (strlen($termino_busqueda) < 3) {
        $error = 'El término de búsqueda debe tener al menos 3 caracteres';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $termino_busqueda)) {
        $error = 'Solo se permiten letras, números, guiones y guiones bajos';
    } else {
        // Realizar búsqueda
        $streamer_encontrado = buscarStreamer($roster, $termino_busqueda);
        $mostrar_resultado = true;
        
        $encontrado = ($streamer_encontrado !== null);
        logBusqueda($termino_busqueda, $encontrado);

        if(!in_array(4, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 4;
            $_SESSION['nivel_usuario']++;

            actualizarProgresoUsuario();
        }


        // Log de búsqueda
        $encontrado = ($streamer_encontrado !== null);
        logBusqueda($termino_busqueda, $encontrado);
        
        // Completar desafío si no estaba completado
        if(!in_array(4, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 4;
            $_SESSION['nivel_usuario']++;
        }
    }
}

mostrarHeader("Desafío 4 - Rankings y Búsqueda");
?>

<main class="container">
    <section class="desafio-section">
        <h2>🏆 Desafío 4: Rankings y Búsqueda de Legends</h2>
        <p>Crea rankings oficiales y busca streamers específicos en tu crew.</p>
        
        <?php formularioRankings($error, $termino_busqueda); ?>
        
        <?php if ($mostrar_resultado): ?>
            <div class="resultado-section">
                <?php mostrarResultadoBusqueda($streamer_encontrado, $termino_busqueda); ?>
            </div>
        <?php endif; ?>
        
        <div class="rankings-container">
            <div class="rankings-grid">
                <?php mostrarRankingFollowers($ranking_followers); ?>
                <?php mostrarRankingAlfabetico($ranking_alfabetico); ?>
            </div>
        </div>
        
        <?php if ($mostrar_resultado || isset($_POST['buscar'])): ?>
            <div class="desafio-completado">
                <p class="success">✅ Búsqueda registrada en el log correctamente</p>
                <p class="success">🎉 ¡Desafío completado! Nivel subido a <?php echo obtenerNivelUsuario(); ?></p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
mostrarFooter();
?>

