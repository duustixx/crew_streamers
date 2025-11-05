<?php
include 'config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$resultado = '';
$ganadores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewers'])) {
    $viewers = trim($_POST['viewers']);
    
    // Validación
    if (empty($viewers)) {
        $error = "¡Ops! Debes ingresar el número de viewers.";
    } elseif (!is_numeric($viewers)) {
        $error = "¡Ops! Debe ser un número válido.";
    } else {
        $viewers = (int)$viewers;
        $viewers = filter_var($viewers, FILTER_SANITIZE_NUMBER_INT);
        
        if ($viewers < 50 || $viewers > 200) {
            $error = "¡Ops! El chat debe tener entre 50 y 200 viewers.";
        } else {
            // Validación pasada
            $_SESSION['viewers_chat'] = $viewers;
            
            // Obtener avatares directamente
            $avatares = glob('Imagenes/streamers/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            
            // Verificar que hay avatares
            if (empty($avatares)) {
                $error = "❌ Error: No se encontraron avatares en la carpeta 'images/streamers/'.";
            } else {
                // Calcular número de ganadores (entre 5 y viewers/10)
                $max_ganadores = max(5, floor($viewers / 10));
                $max_ganadores = min($max_ganadores, count($avatares));
                $num_ganadores = rand(5, $max_ganadores);
                
                $resultado = "¡Felicidades a los $num_ganadores ganadores del sorteo!";
                
                // Seleccionar avatares aleatorios
                $indices_aleatorios = array_rand($avatares, $num_ganadores);
                
                // Si solo hay un ganador, array_rand devuelve un escalar
                if ($num_ganadores == 1) {
                    $ganadores = [$avatares[$indices_aleatorios]];
                } else {
                    $ganadores = [];
                    foreach ($indices_aleatorios as $indice) {
                        $ganadores[] = $avatares[$indice];
                    }
                }
                
                // Guardar en log
                $fecha = date('Y-m-d H:i:s');
                $log_msg = "[$fecha] Sorteo - Viewers: $viewers, Ganadores: $num_ganadores";
                escribirTXT('logs/sorteos.txt', $log_msg);
                
                // Marcar desafío como completado
                if (!in_array(1, $_SESSION['desafios_completados'])) {
                    $_SESSION['desafios_completados'][] = 1;
                    $_SESSION['nivel_usuario']++;
                }
            }
        }
    }
}

// Funciones header y footer
mostrarHeader("Desafío 1 - Chat Rápido");
?>

<main class="dashboard">
    <h1>🎯 Desafío 1 - El Reto del Chat Rápido</h1>
    
    <div class="challenge-container">
        <div class="form-section">
            <h2>Configuración del Sorteo</h2>
            
            <form method="POST">
                <label for="viewers">¿Cuántos viewers hay en el chat? (50-200)</label>
                <input type="number" id="viewers" name="viewers" 
                       value="<?php echo isset($_SESSION['viewers_chat']) ? $_SESSION['viewers_chat'] : ''; ?>">
                
                <?php if ($error): ?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit">🎲 Realizar Sorteo</button>
            </form>
        </div>
        
        <?php if ($resultado): ?>
        <div class="result-section">
            <h2><?php echo $resultado; ?></h2>
            <div class="ganadores-grid">
                <?php
                if (isset($ganadores)) {
                    foreach ($ganadores as $ganador) {
                        echo "<img src='$ganador' alt='Ganador' class='avatar'>";
                    }
                }
                ?>
            </div>
            <p class="success">✅ Sorteo registrado en el log correctamente</p>
            <p class="success">🎉 ¡Desafío completado! Nivel subido a <?php echo $_SESSION['nivel_usuario']; ?></p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php
mostrarFooter();
?>