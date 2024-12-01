<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../src/Controllers/ScoreController.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../../src/Views/login.php");
    exit();
}

// Obtener el ID del usuario
$userId = $_SESSION['user_id'];

// Obtener la puntuación máxima del usuario
try {
    $maxScore = ScoreController::getUserHighScore($userId);
} catch (Exception $e) {
    $maxScore = 0;
    error_log("Error al obtener la puntuación máxima: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Invaders - GameZone</title>
    <link rel="stylesheet" href="../assets/css/space_invaders.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/space_invaders.js" defer></script>
    <meta name="user-id" content="<?php echo htmlspecialchars($userId); ?>">
    <meta name="max-score" content="<?php echo htmlspecialchars($maxScore); ?>">
</head>
<body>
    <!-- Contenedor para el logo -->
    <div class="logo-container">
        <img src="../assets/img/logo.png" alt="GameZone Logo" class="logo-img">
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../juegos.php">Juegos</a></li>
                <li><a href="../contactos.php">Contacto</a></li>
                <li><a href="../../src/Views/account.php">Cuenta</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor principal con Grid -->
    <main class="main-container">
        <!-- Información y controles del juego -->
        <aside class="info-sidebar">
            <h3>Controles</h3>
            <ul>
                <li>Flecha Izquierda: Mover a la Izquierda</li>
                <li>Flecha Derecha: Mover a la Derecha</li>
                <li>Espacio: Disparar</li>
            </ul>
            <h3>Información del Juego</h3>
            <ul>
                <li>Enemigos Rojos: Vida 20</li>
                <li>Enemigos Azules: Vida 40</li>
                <li>Enemigos Amarillos: Vida 60</li>
                <li>Daño Base: 10</li>
            </ul>
        </aside>

        <!-- Contenedor del juego y puntaje -->
        <div class="game-container">
            <!-- Botón de inicio del juego -->
            <div class="button-container">
                <button id="start-game-button" class="game-button">Iniciar Juego</button>
            </div>

            <!-- Área del juego -->
            <div id="game-area">
                <div id="player"></div>
            </div>

            
            
        </div>
        <!-- Contenedor para la información del puntaje -->
        <aside class="info-sidebar-right">
                
                    <div id="player-score">Puntaje: 0</div>
                    <div id="enemies-destroyed">Enemigos Eliminados: 0</div>
                    <div id="power-ups">Velocidad Incrementada: x0, Velocidad de Disparo: x0, Daño Bala: x0</div>
                
            </aside>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
