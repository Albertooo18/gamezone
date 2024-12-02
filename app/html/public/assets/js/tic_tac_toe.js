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
    let adWatched = false; // Variable para controlar si el anuncio ya fue visto

    // Obtener el ID del usuario desde el HTML
    const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');
    const maxScore = parseInt(document.querySelector('meta[name="max-score"]').getAttribute('content'));
    maxScoreElement.textContent = maxScore;

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
    // Comprobar si hay un ganador
const checkWinner = () => {
    const winningCombinations = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];
    let winner = null;

    // Comprobar las combinaciones ganadoras
    winningCombinations.forEach((combination) => {
        const [a, b, c] = combination;
        if (gameState[a] && gameState[a] === gameState[b] && gameState[a] === gameState[c]) {
            winner = gameState[a];
        }
    });

    // Si hay un ganador
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
                resetGame();
                currentPlayer = 'O'; // La IA comienza después de ganar
                iaMove();
            });
        } else {
            showAlert('Derrota', 'La IA ha ganado. Inténtalo de nuevo.', 'error').then(() => {
                totalLosses++;
                consecutiveWins = 0;
                updateScore();

                if (totalLosses >= 3) {
                    endGame();
                } else {
                    resetGame();
                    currentPlayer = 'X';
                }
            });
        }
        return true;
    }

    // Si no hay ganador y el tablero está lleno, es un empate
    if (!gameState.includes(null)) {
        showCoinFlip(); // Llamar la función de la moneda
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
            confirmButtonText: 'OK',
            customClass: {
                popup: 'custom-popup', // Clase personalizada para el fondo
                confirmButton: 'custom-confirm-button' // Clase personalizada para el botón
            }
        });
    };

    // Mostrar una moneda giratoria antes de decidir quién comienza después de un empate
    const showCoinFlip = () => {
        const coinResult = Math.random() < 0.5 ? 'cara' : 'sello';
        const message = coinResult === 'cara' ? 'Cara, después del empate empiezas tú.' : 'Sello, después del empate empieza la IA.';

        // Mostrar el gif de la moneda en el centro con animación
        const coinGif = document.createElement('img');
        coinGif.src = '../public/assets/img/coin.gif';  // Asegúrate de que la ruta al gif esté bien
        coinGif.alt = 'Moneda girando';
        coinGif.style.width = '165px';  // Un 10% más grande
        coinGif.style.height = '165px'; // Un 10% más grande
        coinGif.style.position = 'fixed';
        coinGif.style.top = '50%';
        coinGif.style.left = '50%';
        coinGif.style.transform = 'translate(-50%, -50%) scale(0)';
        coinGif.style.transition = 'transform 0.5s ease-in-out';
        document.body.appendChild(coinGif);

        // Animación de aparición
        setTimeout(() => {
            coinGif.style.transform = 'translate(-50%, -50%) scale(1)';
        }, 100);

        setTimeout(() => {
            // Animación de desaparición
            coinGif.style.transform = 'translate(-50%, -50%) scale(0)';
            setTimeout(() => {
                document.body.removeChild(coinGif);
                // Notificación de empate con el estilo adecuado
                Swal.fire({
                    title: 'Empate',
                    text: message,
                    icon: 'info',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-popup', // Asegurando que usa la clase personalizada
                        confirmButton: 'custom-confirm-button' // También aplica el estilo personalizado al botón
                    }
                }).then(() => {
                    resetGame();
                    if (coinResult === 'sello') {
                        currentPlayer = 'O';
                        iaMove();  // IA comienza después del empate
                    } else {
                        currentPlayer = 'X';  // Jugador comienza después del empate
                    }
                });
            }, 500);
        }, 2000); // Mostrar el gif por 2 segundos
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
            adWatched = false; // Reiniciar el estado del anuncio visto
            updateScore();
            resetGame();
        });
    };

    // Actualizar la puntuación en el DOM
    const updateScore = () => {
        scoreElement.textContent = playerScore;
        levelElement.textContent = difficultyLevel;
        lossesElement.innerHTML = '&#10084;'.repeat(3 - totalLosses);
        checkExtraLifeOption(); // Comprobar si se debe mostrar la opción de vida extra

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

    // Mostrar la notificación para ver el anuncio automáticamente cuando al jugador le quede solo una vida y no haya visto el anuncio
    const checkExtraLifeOption = () => {
        if (totalLosses === 2 && !gameEnded && !adWatched) {
            Swal.fire({
                title: 'Ver Anuncio',
                text: '¿Quieres ver un anuncio para ganar una vida extra?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Rechazar',
                allowOutsideClick: () => !Swal.isLoading() // Permitir hacer clic fuera para priorizar el botón de reinicio
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simulación de un video de anuncio (en una implementación real, este sería un anuncio real)
                    showAd().then(() => {
                        totalLosses--; // Restar una pérdida (ganar una vida extra)
                        adWatched = true; // Marcar que el anuncio ya fue visto
                        updateScore();  // Actualizar la puntuación en el DOM
                    }).catch(() => {
                        console.error('El anuncio no se completó. No se otorgará una vida extra.');
                    });
                }
            });
        }
    };

    // Función para mostrar el anuncio (esto es una simulación)
    const showAd = () => {
        return new Promise((resolve, reject) => {
            // Aquí es donde integrarías una API real para mostrar anuncios.
            // Simularemos un video con un "timeout" de 5 segundos.
            Swal.fire({
                title: 'Ver Anuncio',
                text: 'Espera a que termine el video para ganar una vida extra.',
                imageUrl: '../public/assets/img/ad-placeholder.jpg',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                allowOutsideClick: false // Bloquear clic fuera mientras se muestra el anuncio
            }).then(() => {
                // Simular que el usuario completó de ver el anuncio.
                resolve();
            }).catch(() => {
                reject();
            });
        });
    };

    // Reiniciar el juego
    const resetGame = () => {
        gameState = Array(9).fill(null);
        gameEnded = false;
        renderBoard();
    };

    resetButton.addEventListener('click', () => {
        Swal.close(); // Cerrar cualquier diálogo de anuncio que esté abierto
        resetGame();
    });
    
    renderBoard();
});
