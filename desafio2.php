<?php 
include 'config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';

// Archivos
$archivo_streamers = 'data/featured_streamers.json';
$invitado_especial = 'Imagenes/streamers/invitado_especial.png';

// 1. Cargar todos los avatares disponibles (para mostrar la lista completa)
$todos_avatares = glob('Imagenes/streamers/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// 2. Cargar o inicializar featured streamers
if(!file_exists($archivo_streamers)) {
    // Si no existe el archivo, crear lista inicial con los primeros 20 avatares
    $streamers_featured = array_slice($todos_avatares, 0, 20);
    guardarJSON($archivo_streamers, $streamers_featured);
} else {
    // Cargar del archivo
    $streamers_featured = leerJSON($archivo_streamers);
}

// 3. Procesar rotación diaria
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rotar'])) {
    // Verificar que hay streamers para rotar
    if(!empty($streamers_featured)) {
        // Eliminar el primer streamer (más antiguo)
        array_shift($streamers_featured);

        // Añadir invitado especial al final
        if(file_exists($invitado_especial)) {
            $streamers_featured[] = $invitado_especial;
        } else {
            // Si no existe invitado_especial.png, usar un avatar aleatorio
            $streamers_featured[] = $todos_avatares[array_rand($todos_avatares)];
        }

        // Guardar el nuevo orden
        guardarJSON($archivo_streamers, $streamers_featured);

        $mensaje = "✅ Lista de featured actualizada correctamente";

        // Marcar desafío como completado
        if(!in_array(2, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 2;
            $_SESSION['nivel_usuario']++;
            
            actualizarProgresoUsuario();
        }
    } else {
        $mensaje = "❌ No hay streamers para rotar";
    }
}

// 4. Procesar reset
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    // Volver a la lista original (primeros 20 avatares)
    $streamers_featured = array_slice($todos_avatares, 0, 20);
    guardarJSON($archivo_streamers, $streamers_featured);

    $mensaje = "✅ Lista reseteada al orden original";
}

// Variables globales para la función
$GLOBALS['numero_streamers'] = count($streamers_featured);

mostrarHeader("Desafío 2 - Featured Streamers");
?>

<main class="dashboard">
    <h1>🔄 Desafío 2 - Rotación de Featured Streamers</h1>

    <div class="challenge-container">
        <?php 
        // Usar la función del config.php
        formularioDesafio2($mensaje, $streamers_featured);
        ?>
        
        <!-- Sección adicional: Todos los Streamers -->
        <div class="streamers-section" style="margin-top: 40px;">
            <h2>📋 Todos los Streamers Disponibles</h2>
            <p style="text-align: center; color: var(--text-muted);">
                Total: <?php echo count($todos_avatares); ?> streamers
            </p>
            <div class="avatars-container">
                <?php foreach($todos_avatares as $index => $avatar): ?>
                    <div class="streamer-card">
                        <img src="<?php echo $avatar; ?>" 
                             alt="Streamer <?php echo ($index + 1); ?>" 
                             class="avatar">
                        <small>Streamer <?php echo ($index + 1); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<?php
mostrarFooter();
?>