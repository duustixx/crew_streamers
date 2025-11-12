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
$mostrar_equipos = false;
$teamChaos = array();
$teamOrder = array();
$totalChaos = 0;
$totalOrder = 0;
$mvp = array();
$rookie = array();

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generar_roster'])) {
        // Generar nuevo roster
        $roster = generarRosterStreamers();
        guardarRoster($roster);
        $resultado = "¡Nuevo roster generado exitosamente!";
        $mostrar_equipos = true;
        
    } elseif (isset($_POST['cargar_roster'])) {
        // Cargar roster existente
        $roster = cargarRoster();
        if (!empty($roster)) {
            $resultado = "Roster cargado exitosamente";
            $mostrar_equipos = true;
        } else {
            $error = "No hay roster guardado. Genera uno nuevo primero.";
        }
    }
    
    if ($mostrar_equipos && isset($roster)) {
        $equipos = dividirEquipos($roster);
        $teamChaos = $equipos['chaos'];
        $teamOrder = $equipos['order'];
        $totalChaos = calcularTotalFollowers($teamChaos);
        $totalOrder = calcularTotalFollowers($teamOrder);
        $mvp = encontrarMVP($roster);
        $rookie = encontrarRookie($roster);
        
        // Registrar en log
        $mensaje_log = "TORNEO - Usuario: " . obtenerUsername() . 
                      ", Team Chaos: " . count($teamChaos) . " streamers, " . $totalChaos . " followers" .
                      ", Team Order: " . count($teamOrder) . " streamers, " . $totalOrder . " followers";
        logAccion($mensaje_log);
        
        // Completar desafío si no estaba completado
        if(!in_array(3, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 3;
            $_SESSION['nivel_usuario']++;
        }
    }
}

mostrarHeader("Desafío 3 - Formación de Equipos");
?>

<main class="container">
    <section class="desafio-section">
        <h2>⚔️ Desafío 3: Formación de Equipos para el Torneo</h2>
        <p>¡Organiza a tus streamers en dos equipos equilibrados y descubre quiénes son los MVP!</p>
        
        <?php formularioTorneos($error, $resultado); ?>
        
        <?php if ($mostrar_equipos): ?>
            <div class="torneo-section">
                <?php mostrarEquiposTorneo($teamChaos, $teamOrder, $totalChaos, $totalOrder, $mvp, $rookie); ?>
                
                <div class="torneo-stats">
                    <p class="success">✅ Torneo registrado en el log correctamente</p>
                    <p class="success">🎉 ¡Desafío completado! Nivel subido a <?php echo obtenerNivelUsuario(); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
mostrarFooter();
?>