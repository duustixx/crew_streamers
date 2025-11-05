<?php
require_once 'config.php';

// Si no tiene un Username, redirigir al formulario
if(!isset($_SESSION['username_gamer']) && !isset($_POST['username'])){
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido al Crew</title>
        <link rel="stylesheet" href="css/gaming-styles.css">
    </head>
    <body class="dark-theme">
        <div class="login-container">
            <h1>🎮 Únete al Crew</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Elige tu username gamer:</label>
                    <input type="text" id="username" name="username" required 
                           pattern="[a-zA-Z0-9_-]{3,20}" 
                           title="Solo letras, números, guiones y guiones bajos (3-20 caracteres)">
                </div>
                <button type="submit" class="btn-neon">🚀 Entrar al Dashboard</button>
            </form>
        </div>
    </body>
    </html>
<?php
    exit;
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

$username = $_SESSION['username_gamer'];

if(isset($_COOKIE['racha_dias'])){
    $racha = $_COOKIE['racha_dias'];
} else{
    $racha = 1;
}

if(isset($_COOKIE['tema'])){
    $tema_actual = $_COOKIE['tema'];
} else{
    $tema_actual = 'dark';
}

// Mostrar header usando la función de config.php
mostrarHeader("Dashboard - " . $username);
?>

<main class="container">
    <section class="welcome-section">
        <h2>¡Bienvenido de nuevo, <?php echo $username; ?>! 👋</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🔥 Racha Actual</h3>
                <p class="stat-number"><?php echo $racha; ?> días</p>
            </div>
            <div class="stat-card">
                <h3>👥 Total Visitas</h3>
                <p class="stat-number"><?php echo $visitas; ?></p>
            </div>
            <div class="stat-card">
                <h3>🎯 Desafíos Completados</h3>
                <p class="stat-number"><?php echo count($_SESSION['desafios_completados']); ?>/5</p>
            </div>
        </div>
    </section>

    <section class="streamers-section">
        <h3>🎮 Tu Crew de Streamers:</h3>
        <div class="streamers-grid">
            <?php
            $avatares = glob('imagenes/streamers/*.{png,jpg,jpeg,gif}', GLOB_BRACE);
            $numero_avatares = count($avatares);
            $mostrar = 20;
            if($numero_avatares < 20){
                $mostrar = $numero_avatares;
            }

            for($i = 0; $i < $mostrar; $i++){
                echo '<div class="avatar-card">';
                echo '<img src="' . $avatares[$i] . '" alt="Streamer' . ($i+1) . '">';
                echo '<span>Streamer ' . ($i+1) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </section>
</main>

<?php
// Mostrar footer usando la función de config.php
mostrarFooter();
?>
