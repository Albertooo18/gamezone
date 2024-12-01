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
    <title>3 en Raya - GameZone</title>
    <link rel="stylesheet" href="../assets/css/tic_tac_toe.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/tic_tac_toe.js" defer></script>
    <meta name="user-id" content="<?php echo htmlspecialchars($userId); ?>">
    <meta name="max-score" content="<?php echo htmlspecialchars($maxScore); ?>">
</head>
<body>
    <!-- Contenedor para el logo -->
    <div class="logo-container">
        <img src="../assets/img/logo.png" alt="GameZone Logo">
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

    <!-- Contenedor principal -->
    <main class="main-container">
        <div class="side-wrapper">
            <div class="side-container">
                <!-- Contenedores individuales para la información del jugador -->
                <div class="info-card">
                    <h3>Puntuación</h3>
                    <p><span id="player-score">0</span></p>
                </div>
                <div class="info-card">
                    <h3>Puntuación Máxima</h3>
                    <p><span id="max-player-score"><?php echo htmlspecialchars($maxScore); ?></span></p>
                </div>
                <div class="info-card">
                    <h3>Nivel Actual</h3>
                    <p><span id="current-level">1</span></p>
                </div>
                <div class="info-card">
                    <h3>Vidas</h3>
                    <p><span id="total-losses">&#10084;&#10084;&#10084;</span></p>
                </div>
            </div>
        </div>

        <!-- Contenedor del juego "3 en Raya" en el centro -->
        <div class="game-container">
            <div class="tic-tac-toe-container">
                <h2 class="tictactoe-titulo">3 en Raya</h2>
                <div id="tic-tac-toe-board" class="tictactoe-tablero">
                    <!-- Celdas del tablero serán generadas dinámicamente -->
                </div>
                <button id="reset-button" class="tictactoe-reset-btn">Reiniciar Juego</button>
            </div>
        </div>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>

