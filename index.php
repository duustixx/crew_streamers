<?php
require_once 'config.php';

// Verificar si se solicitó logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    logout();
}

// Si no tiene un Username, redirigir al formulario
if(!isset($_SESSION['username_gamer']) && !isset($_POST['username'])){
    mostrarLogin();
}

// Procesar formulario de Username
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])){
    $username = trim($_POST['username']);

    // Validamos
    if(empty($username)){
        $error = 'Debes ingresar un username';
    } elseif(!preg_match('/^[a-zA-Z0-9_-]{3,20}$/',$username)){
        $error = "Solo se permiten letras, números, guiones y guiones bajos (3-20 caracteres)";
    } else{
        $_SESSION['username_gamer'] = htmlspecialchars($username);
        header('Location: index.php');
        exit;
    }
}

// Obtener datos usando las funciones de config.php
$username = obtenerUsername();
$racha = obtenerRacha();
$desafios_completados = obtenerDesafiosCompletados();

// Mostrar header usando la función de config.php
mostrarHeader("Dashboard - " . $username);
?>

<main class="container">
    <?php
    mostrarSeccionBienvenida($username,$racha,$visitas,$desafios_completados);
    mostrarSeccionStreamers();
    ?>
</main>

<?php
// Mostrar footer usando la función de config.php
mostrarFooter();
