<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juegos - GameZone</title>
    <!-- Asegúrate de que esta ruta sea correcta -->
    <link rel="stylesheet" href="assets/css/juegos.css">
</head>
<body>
    <!-- Contenedor para el logo -->
    <div class="logo-container">
        <img src="assets/img/logo.png" alt="GameZone Logo">
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <li><a href="contactos.php">Contacto</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="../src/Views/account.php">Cuenta</a></li>
                <?php else: ?>
                    <li><a href="../src/Views/login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="juegos-titulo">Nuestros Juegos</h1>
        <p class="juegos-descripcion">Aquí puedes encontrar todos nuestros juegos disponibles.</p>
        <div class="galeria-juegos">
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/cook.webp" alt="Juego 1">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/jungle.webp" alt="Juego 2">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/racing.webp" alt="Juego 3">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/robot.webp" alt="Juego 4">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/skate.webp" alt="Juego 5">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/space.webp" alt="Juego 6">
            </a>
            <a href="tic_tac_toe.php">
                <img class="juego-imagen" src="assets/img/tictactoe.webp" alt="Juego 3 en Raya">
            </a>
            <a href="dev.php">
                <img class="juego-imagen" src="assets/img/underwater.webp" alt="Juego 8">
            </a>
        </div>

    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
