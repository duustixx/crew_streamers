<?php
require_once 'config.php';

// Verificar si se solicitó logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    logout();
}

// SI NO ESTÁ LOGUEADO, MOSTRAR LOGIN/REGISTRO
if (!isset($_SESSION['username_gamer'])) {
    
    // VERIFICAR SI VIENE DE FORMULARIO DE REGISTRO/LOGIN
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // PROCESAR REGISTRO
        if (isset($_POST['registro'])) {
            procesarRegistro();
        }
        
        // PROCESAR LOGIN
        if (isset($_POST['login'])) {
            procesarLogin();
        }
    }
    
    // VERIFICAR QUÉ MOSTRAR (LOGIN O REGISTRO)
    if (isset($_GET['registro'])) {
        mostrarFormularioRegistro();
    } else {
        mostrarFormularioLogin();
    }
    exit; // Importante salir aquí
}

// SI ESTÁ LOGUEADO, MOSTRAR DASHBOARD

// Inicializar aplicación
inicializarAplicacion();

// Obtener datos usando las funciones de config.php
$username = obtenerUsername();
$racha = obtenerRacha();
$desafios_completados = obtenerDesafiosCompletados();

// Mostrar header usando la función de config.php
mostrarHeader("Dashboard - " . $username);
?>

<main class="container">
    <?php
    mostrarSeccionBienvenida($username,$visitas, $desafios_completados);
    mostrarSeccionStreamers();
    ?>
</main>

<?php
// Mostrar footer usando la función de config.php
mostrarFooter();
?>
