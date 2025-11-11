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
$resultado = '';
$ranking_data = array();

// Procesar formulario de rankings
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['streamers_data'])) {
    $streamers_data = trim($_POST['streamers_data']);
    
    // Validar
    if(empty($streamers_data)) {
        $error = 'Debes ingresar los datos de los streamers';
    } else {
        // Procesar rankings
        $ranking_data = procesarRankings($streamers_data);
        
        if(!empty($ranking_data)) {
            $resultado = "¡Ranking generado correctamente!";
            
            // Registrar en log
            $mensaje_log = "RANKING - Usuario: " . obtenerUsername() . ", Streamers procesados: " . count($ranking_data);
            logAccion($mensaje_log);
            
            // Completar desafío si no estaba completado
            if(!in_array(4, $_SESSION['desafios_completados'])) {
                $_SESSION['desafios_completados'][] = 4;
                $_SESSION['nivel_usuario']++;
            }
        } else {
            $error = 'Error al procesar los datos. Verifica el formato.';
        }
    }
}

mostrarHeader("Desafío 4 - Rankings");
?>

<main class="container">
    <section class="desafio-section">
        <h2>🏆 Desafío 4: Sistema de Rankings</h2>
        <p>Genera rankings de streamers basados en sus estadísticas de viewers.</p>
        
        <?php formularioRankings($error, $resultado, $ranking_data); ?>
    </section>
</main>

<?php
mostrarFooter();
?>
