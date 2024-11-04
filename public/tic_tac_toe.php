<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3 en Raya - GameZone</title>
    <link rel="stylesheet" href="assets/css/tic_tac_toe.css">
</head>
<body>
    <!-- Contenedor para el logo -->
    <div class="logo-container">
        <img src="assets/img/nombre.png" alt="GameZone Logo">
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <li><a href="contactos.php">Contacto</a></li>
                <li><a href="../src/Views/login.php">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor del juego "3 en Raya" -->
    <div class="tic-tac-toe-container">
        <h2 class="tictactoe-titulo">3 en Raya</h2>
        <div id="tic-tac-toe-board" class="tictactoe-tablero">
            <!-- Celdas del tablero serán generadas dinámicamente -->
        </div>
        <button id="reset-button" class="tictactoe-reset-btn">Reiniciar Juego</button>
    </div>

    <script src="assets/js/tic_tac_toe.js"></script>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
