<?php
session_start();
require_once '../src/Controllers/ScoreController.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: ../src/Views/login.php");
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
    <link rel="stylesheet" href="assets/css/tic_tac_toe.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <li><a href="contactos.php">Contacto</a></li>
                <li><a href="../src/Views/account.php">Cuenta</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor del juego "3 en Raya" -->
    <div class="game-container">
        <div class="tic-tac-toe-container">
            <h2 class="tictactoe-titulo">3 en Raya</h2>
            <div id="tic-tac-toe-board" class="tictactoe-tablero">
                <!-- Celdas del tablero serán generadas dinámicamente -->
            </div>
            <button id="reset-button" class="tictactoe-reset-btn">Reiniciar Juego</button>
        </div>

        <!-- Contenedores adicionales para la información -->
        <div class="info-containers">
            <!-- Puntuación -->
            <div class="score-container">
                <p>Puntuación: <span id="player-score">0</span></p>
            </div>

            <!-- Puntuación Máxima -->
            <div class="max-score-container">
                <p>Puntuación Máxima: <span id="max-player-score"><?php echo htmlspecialchars($maxScore); ?></span></p>
            </div>

            <!-- Nivel Actual -->
            <div class="level-container">
                <p>Nivel Actual: <span id="current-level">1</span></p>
            </div>

            <!-- Derrotas Totales con corazones -->
            <div class="losses-container">
                <p>Derrotas Totales: <span id="total-losses">&#10084;&#10084;&#10084;</span></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const board = document.getElementById('tic-tac-toe-board');
            const resetButton = document.getElementById('reset-button');
            const scoreElement = document.getElementById('player-score');
            const maxScoreElement = document.getElementById('max-player-score');
            const levelElement = document.getElementById('current-level');
            const lossesElement = document.getElementById('total-losses');

            let currentPlayer = 'X';
            let gameState = Array(9).fill(null);
            let playerScore = 0;
            let totalLosses = 0;
            let consecutiveWins = 0;
            let difficultyLevel = 1;
            let gameEnded = false;

            // Obtener el ID del usuario desde PHP
            const userId = <?php echo json_encode($userId); ?>;

            // Renderizar el tablero
            const renderBoard = () => {
                board.innerHTML = '';
                board.style.display = 'grid';
                board.style.gridTemplateColumns = 'repeat(3, 100px)';
                board.style.gridGap = '10px';
                gameState.forEach((cell, index) => {
                    const cellElement = document.createElement('div');
                    cellElement.classList.add('cell');
                    cellElement.dataset.index = index;
                    cellElement.textContent = cell;
                    if (!gameEnded) {
                        cellElement.addEventListener('click', handleCellClick);
                    }
                    board.appendChild(cellElement);
                });
            };

            // Manejar clics en las celdas (Movimiento del Jugador)
            const handleCellClick = (e) => {
                const index = e.target.dataset.index;
                if (!gameState[index] && currentPlayer === 'X') {
                    gameState[index] = currentPlayer;
                    renderBoard();
                    if (!checkWinner()) {
                        currentPlayer = 'O'; // Cambia al turno de la IA
                        setTimeout(iaMove, 500);
                    }
                }
            };

            // Movimiento de la IA basado en el nivel de dificultad
            const iaMove = () => {
                if (gameEnded) return;

                let emptyCells = gameState
                    .map((cell, index) => (cell === null ? index : null))
                    .filter(index => index !== null);

                if (emptyCells.length > 0) {
                    let selectedCell;

                    if (difficultyLevel === 1) {
                        selectedCell = emptyCells[Math.floor(Math.random() * emptyCells.length)];
                    } else if (difficultyLevel === 2) {
                        selectedCell = findBlockingMove() || emptyCells[Math.floor(Math.random() * emptyCells.length)];
                    } else if (difficultyLevel === 3) {
                        selectedCell = findWinningMove() || findBlockingMove() || emptyCells[Math.floor(Math.random() * emptyCells.length)];
                    }

                    gameState[selectedCell] = 'O';
                    renderBoard();
                    if (!checkWinner()) {
                        currentPlayer = 'X';
                    }
                }
            };

            // Buscar un movimiento crítico para bloquear o ganar
            const findCriticalMove = (player) => {
                const winningCombinations = [
                    [0, 1, 2], [3, 4, 5], [6, 7, 8],
                    [0, 3, 6], [1, 4, 7], [2, 5, 8],
                    [0, 4, 8], [2, 4, 6]
                ];
                for (const [a, b, c] of winningCombinations) {
                    if (gameState[a] === player && gameState[b] === player && gameState[c] === null) return c;
                    if (gameState[a] === player && gameState[c] === player && gameState[b] === null) return b;
                    if (gameState[b] === player && gameState[c] === player && gameState[a] === null) return a;
                }
                return null;
            };

            // Utilidades para encontrar bloqueos o movimientos ganadores
            const findBlockingMove = () => findCriticalMove('X');
            const findWinningMove = () => findCriticalMove('O');

            // Comprobar si hay un ganador
            const checkWinner = () => {
                const winningCombinations = [
                    [0, 1, 2], [3, 4, 5], [6, 7, 8],
                    [0, 3, 6], [1, 4, 7], [2, 5, 8],
                    [0, 4, 8], [2, 4, 6]
                ];
                let winner = null;

                winningCombinations.forEach((combination) => {
                    const [a, b, c] = combination;
                    if (gameState[a] && gameState[a] === gameState[b] && gameState[a] === gameState[c]) {
                        winner = gameState[a];
                    }
                });

                if (winner) {
                    if (winner === 'X') {
                        showAlert('¡Felicidades!', 'Has ganado.', 'success').then(() => {
                            playerScore += 10;
                            consecutiveWins++;
                            if (consecutiveWins >= 2) {
                                difficultyLevel = Math.min(3, difficultyLevel + 1);
                                consecutiveWins = 0;
                            }
                            updateScore();
                            currentPlayer = 'O'; // Hacer que la IA empiece en el próximo juego
                            resetGame(); // Reiniciar el tablero después de ganar
                            iaMove(); // IA hace el primer movimiento
                        });
                    } else {
                        showAlert('Derrota', 'La IA ha ganado. Inténtalo de nuevo.', 'error').then(() => {
                            totalLosses++;
                            consecutiveWins = 0;
                            updateScore();

                            if (totalLosses >= 3) {
                                endGame();
                            } else {
                                currentPlayer = 'X'; // Hacer que el jugador empiece en el próximo juego
                                resetGame(); // Reiniciar el tablero después de perder
                            }
                        });
                    }
                    return true;
                }

                if (!gameState.includes(null)) {
                    showAlert('Empate', 'Es un empate.', 'info').then(() => {
                        playerScore += 2;
                        updateScore();
                        currentPlayer = 'O'; // Hacer que la IA empiece en el próximo juego
                        resetGame(); // Reiniciar el tablero después de un empate
                        iaMove(); // IA hace el primer movimiento
                    });
                    return true;
                }

                return false;
            };

            // Mostrar una alerta con SweetAlert2 y devolver una promesa
            const showAlert = (title, text, icon) => {
                return Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    confirmButtonText: 'OK'
                });
            };

            // Finalizar el juego después de 3 derrotas
            const endGame = () => {
                gameEnded = true;

                // Guardar puntuación antes de finalizar el juego
                saveScore(playerScore);

                Swal.fire({
                    title: 'Juego Terminado',
                    text: 'Has alcanzado 3 derrotas. El juego ha finalizado.',
                    icon: 'error',
                    confirmButtonText: 'Reiniciar'
                }).then(() => {
                    // Reiniciar puntuaciones y estado del juego
                    playerScore = 0;
                    totalLosses = 0;
                    difficultyLevel = 1;
                    gameEnded = false;
                    updateScore();
                    resetGame();
                });
            };

            // Actualizar la puntuación en el DOM
            const updateScore = () => {
                scoreElement.textContent = playerScore;
                levelElement.textContent = difficultyLevel;
                lossesElement.innerHTML = '&#10084;'.repeat(3 - totalLosses);

                if (playerScore > parseInt(maxScoreElement.textContent)) {
                    maxScoreElement.textContent = playerScore;
                }
            };

            // Enviar la puntuación al backend utilizando fetch
            const saveScore = (score) => {
                if (!userId) {
                    console.error("No se puede enviar la puntuación sin un ID de usuario.");
                    return;
                }

                console.log("Enviando datos al backend:", {
                    action: 'saveScore',
                    user_id: userId,
                    game_id: 1,
                    score: score
                });

                fetch('../src/Controllers/ScoreController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'saveScore',
                        user_id: userId,
                        game_id: 1,
                        score: score
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Puntuación guardada correctamente");
                    } else {
                        console.error("Error al guardar la puntuación:", data.error);
                    }
                })
                .catch(error => {
                    console.error("Hubo un error:", error);
                });
            };

            // Reiniciar el juego
            const resetGame = () => {
                gameState = Array(9).fill(null);
                currentPlayer = 'X';
                gameEnded = false;
                renderBoard();
            };

            resetButton.addEventListener('click', resetGame);
            renderBoard();
        });
    </script>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
