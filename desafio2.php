<?php 
include 'config.php';

//Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';

//Archivo donde guardamos el orden de los streamers
$archivo_streamers = 'data/featured_streamers.json';


//1. Cargar o inicializar streamers
if(!file_exists($archivo_streamers)) {
    //Si no existe el archivo, crear lista inicial con los avatares
    $avatares = glob('Imagenes/streamers/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $streamers = array_slice($avatares, 0, 20); //Que coga máximo 20
    guardarJSON($archivo_streamers, $streamers);
} else {
    //Cargar del archivo
    $streamers = leerJSON($archivo_streamers);
}

//2. Procesar rotación diaria
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rotar'])) {
    //Verificar que hay streamers para rotar
    if(!empty($streamers)) {
        //ELiminar el primer streamer (más antiguo)
        array_shift($streamers);

        //Añadir nuevo streamer (usamos el primero de la lista como "invitado")
        if(!empty($avatares)) {
            $streamers[] = $avatares[0]; //Usamos el primer avatar como invitado
        }

        //Guardar el nuevo orden
        guardarJSON($archivo_streamers, $streamers);

        $mensaje = "✅ Lista de featured actualizada correctamente";

        if(!in_array(2, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'];
            $_SESSION['nivel_usuario']++;

            actualizarProgresoUsuario();
        }
    } else {
        $mensaje = "❌ No hay streamers para rotar";
    }
}

//3. Procesar reset
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    //Volver a la lista original
    $avatares = glob('Imagenes/streamers/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $streamers = array_slice($avatares, 0, 20);
    guardarJSON($archivo_streamers, $streamers);

    $mensaje = "✅ Lista reseteada al orden original";
}

//Variables globales para la función - SOLO SI HAY STREAMERS
if(is_array($streamers)) {
    $GLOBALS['numero_streamers'] = count($streamers);
} else {
    $GLOBALS['numero_streamers'] = 0;
}

mostrarHeader("Desafio 2 - Featured Streamers");
?>

<main class="dashboard">
    <h1>🔄 Desafío 2 - Rotación de Featured Streamers</h1>

    <div class="challenge-container">
        <?php 
        //Usar la función del config.php
        formularioDesafio2($mensaje, $streamers);
        ?>
    </div>
</main>