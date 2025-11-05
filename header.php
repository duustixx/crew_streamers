<?php 
if(!isset($_SESSION['username_gamer'])) return;
?>
<header class="gaming-header">
    <h1> Crew Manager</h1>
    <div class="user-info">
        <span>Bienvenido, <?php echo $_SESSION['username_gamer']; ?></span>
        <span>Nivel: <?php echo $_SESSION['nivel_usuario']; ?></span>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</header>

<nav class="gaming-nav">
    <a href="index.php">Home</a>
    <a href="desafio1.php">🎯 Desafio 1 - Chat Rápido</a>
    <a href="desafio2.php">🔥 Desafio 2 - Featured Streamers</a>
    <a href="desafio3.php">⚡ Desafio 3 - Formación de equipos</a>
    <a href="desafio4.php">🏆 Desafio 4 - Rankings</a>
    <a href="desafio5.php">💎 Desafio 5 - Sponsors</a>
</nav>