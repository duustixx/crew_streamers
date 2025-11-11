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

// Procesar formulario de torneos
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['equipos'])) {
    $equipos = trim($_POST['equipos']);
    
    // Validar
    if(empty($equipos)) {
        $error = 'Debes ingresar el número de equipos';
    } elseif(!is_numeric($equipos) || $equipos < 4 || $equipos > 16) {
        $error = 'El número de equipos debe ser entre 4 y 16';
    } else {
        // Generar enfrentamientos
        $enfrentamientos = generarEnfrentamientosTorneo($equipos);
        $resultado = "¡Torneo configurado con $equipos equipos!";
        
        // Registrar en log
        $mensaje_log = "TORNEO - Usuario: " . obtenerUsername() . ", Equipos: $equipos";
        logAccion($mensaje_log);
        
        // Completar desafío si no estaba completado
        if(!in_array(3, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 3;
            $_SESSION['nivel_usuario']++;
        }
    }
}

mostrarHeader("Desafío 3 - Torneos");
?>

<main class="container">
    <section class="desafio-section">
        <h2>⚔️ Desafío 3: Organizador de Torneos</h2>
        <p>Configura un torneo gaming y genera los enfrentamientos automáticamente.</p>
        
        <?php formularioTorneos($error, $resultado); ?>
    </section>
</main>

<?php
mostrarFooter();
?>