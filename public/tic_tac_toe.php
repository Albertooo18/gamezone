<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3 en Raya - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Contenedor para el logo (con enlace a la página principal) -->
    <div class="logo-container">
        <a href="index.php">
            <img src="assets/img/nombre.png" alt="GameZone Logo">
        </a>
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <li><a href="contacto.php">Contacto</a></li>
                <li><a href="../src/Views/login.php">Iniciar Sesión</a></li> <!-- Enlace al login -->
            </ul>
        </nav>
    </header>

    <!-- Contenedor del juego "3 en Raya" -->
    <div class="tic-tac-toe-container">
        <h2>3 en Raya</h2>
        <div id="tic-tac-toe-board"></div>
        <button id="reset-button">Reiniciar Juego</button>
    </div>

    <script src="assets/js/tic_tac_toe.js"></script>
</body>
</html>
