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

// Verificar descarga de CSV
if (isset($_GET['descargar_csv']) && $_GET['descargar_csv'] == 'true') {
    descargarReporteCSV();
}

$error = '';
$success = '';

// Cargar roster existente o generar uno si no existe
$roster = cargarRoster();
if (empty($roster)) {
    $roster = generarRosterStreamers();
    guardarRoster($roster);
}

// Cargar sponsors
$sponsors = cargarSponsors();

// Procesos del desafío
$juegos_string = obtenerJuegosFavoritos($roster);
$roster_con_sponsors = asignarSponsors($roster, $sponsors);
guardarColaboracionesCSV($roster_con_sponsors);

// Procesar formulario de nuevo sponsor
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['añadir_sponsor'])) {
    $nuevo_sponsor = trim($_POST['nuevo_sponsor']);
    
    // Validaciones
    if (empty($nuevo_sponsor)) {
        $error = 'Debes ingresar el nombre del sponsor';
    } elseif (strlen($nuevo_sponsor) < 3) {
        $error = 'El nombre del sponsor debe tener al menos 3 caracteres';
    } elseif (strlen($nuevo_sponsor) > 50) {
        $error = 'El nombre del sponsor no puede tener más de 50 caracteres';
    } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $nuevo_sponsor)) {
        $error = 'Solo se permiten letras, números y espacios';
    } else {
        // Añadir sponsor al archivo
        $sponsors_actuales = file_get_contents('data/sponsors.txt');
        $nuevo_contenido = $sponsors_actuales . '; ' . $nuevo_sponsor;
        file_put_contents('data/sponsors.txt', $nuevo_contenido);
        
        $success = '✅ Sponsor "' . $nuevo_sponsor . '" añadido correctamente';
        
        // Recargar sponsors
        $sponsors = cargarSponsors();
        $roster_con_sponsors = asignarSponsors($roster, $sponsors);
        guardarColaboracionesCSV($roster_con_sponsors);

        if (empty($sponsors)) {
        // Si no hay sponsors, crear algunos por defecto
        $sponsors_default = "Red Bull Gaming;Logitech G;HyperX;Razer;NVIDIA;Corsair;SteelSeries";
        file_put_contents('data/sponsors.txt', $sponsors_default);
            $sponsors = cargarSponsors(); // Recargar
        }
        
        // Registrar en log
        $mensaje_log = "SPONSOR - Usuario: " . obtenerUsername() . ", Nuevo sponsor: " . $nuevo_sponsor;
        logAccion($mensaje_log);
        
        // Completar desafío si no estaba completado
        if(!in_array(5, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 5;
            $_SESSION['nivel_usuario']++;
        }
    }
}

mostrarHeader("Desafío 5 - Gestión de Sponsors");
?>

<main class="container">
    <section class="desafio-section">
        <h2>💎 Desafío 5: Gestión de Sponsors y Colaboraciones</h2>
        <p>Gestiona sponsors y colaboraciones para tu crew en crecimiento.</p>
        
        <div class="sponsors-grid-layout">
            <div class="sponsors-sidebar">
                <?php mostrarJuegosTrending($juegos_string); ?>
                <?php mostrarListaSponsors($sponsors); ?>
                <?php formularioSponsors($error, $success); ?>
                
                <div class="acciones-sponsors">
                    <a href="?descargar_csv=true" class="btn-download">📥 Descargar Reporte de Colaboraciones</a>
                </div>
            </div>
            
            <div class="sponsors-main">
                <?php mostrarTablaColaboraciones($roster_con_sponsors); ?>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="desafio-completado">
                <p class="success">✅ Sponsor registrado en el log correctamente</p>
                <p class="success">🎉 ¡Desafío completado! Nivel subido a <?php echo obtenerNivelUsuario(); ?></p>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php
mostrarFooter();
?>