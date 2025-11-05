<?php 
include 'config.php';

//Verificar que el usuario esté logueado
if(!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$resultado = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewers'])) {
    $viewers = trim($_POST['viewers']);

    //Validación
    if(empty($viewers)) {
        $error = "Debes ingresar el número de viewers.";
    } else if (!is_numeric($viewers)) {
        $error = "Debes introducir un número valido.";
    } else {
        $viewers = (int)$viewers;
        $viewers = filter_var($viewers, FILTER_SANITIZE_NUMBER_INT);

        if($viewers < 50 || $viewers > 200) {
            $error = "El chat debe tener entre 50 y 200 viewers.";
        } else {
            //Validación pasada
            $_SESSION['viewers_chat'] = $viewers;

            //Calcular número de ganadores (entre 5 y viewers/10)
            $max_ganadores = max(5, floor($viewers / 10));
            $num_ganadores = rand(5, $max_ganadores);

            //Seleccionar avatares aleatorios
            $avatares = glob('images/streamers/*.{.jpg,jpeg,png,gif', GLOB_BRACE);
            $ganadores = [];

            for($i = 0; $i < $num_ganadores; $i++) {
                $ganadores[] = $avatares[array_rand($avatares)];
            }

            //Guardar en log
            $fecha = date('Y-m-d H:i:s');
            $log_msg = "[$fecha] Sorteo - Viewers: $viewers, Ganadores: $num_ganadores";
            escribirTXT('logs/sorteos.txt', $log_msg);

            //Marcar desafío como completado
            if(!in_array(1, $_SESSION['desafios_completados'])) {
                $_SESSION['desafios_completados'][] = 1;
                $_SESSION['nivel_usuario']++;
            }

            $resultado = "Felicidades a los $num_ganadores ganadores del sorteo!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desafio 1 - Chat rápido</title>
    <link rel="stylesheet" href="css/gaming-styles.css">
</head>
<body class="dark-theme">
    <?php include 'header.php'; ?>

    <main class="dashboard">
        <h1>🎯 Desafio 1 - El Reto del Chat Rápido </h1>

        <div class="challenge-container">
            <div class="form-section">
                <h2>Configuración del sorteo</h2>
                 <form method="POST">
                    <label for="viewers">Cuántos viewers hay en el chat? (50-200)</label>
                    <input type="number" id="viewers" name="viewers" 
                           min="50" max="200" required
                           value="<?php echo isset($_SESSION['viewers_chat']) ? $_SESSION['viewers_chat'] : ''; ?>">
                    
                    <?php if ($error): ?>
                        <div class="error"><?php echo $error; ?></div>
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
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>