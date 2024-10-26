document.addEventListener('DOMContentLoaded', () => {
    const board = document.getElementById('tic-tac-toe-board');
    const resetButton = document.getElementById('reset-button');
    let currentPlayer = 'X';
    let gameState = Array(9).fill(null);

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
            cellElement.addEventListener('click', handleCellClick);
            board.appendChild(cellElement);
        });
    };

    // Manejar clics en las celdas
    const handleCellClick = (e) => {
        const index = e.target.dataset.index;
        if (!gameState[index]) {
            gameState[index] = currentPlayer;
            currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
            renderBoard();
            checkWinner();
        }
    };

    // Comprobar si hay un ganador
    const checkWinner = () => {
        const winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6]
        ];
        winningCombinations.forEach((combination) => {
            const [a, b, c] = combination;
            if (gameState[a] && gameState[a] === gameState[b] && gameState[a] === gameState[c]) {
                alert(`Jugador ${gameState[a]} ha ganado!`);
                resetGame();
            }
        });
    };

    // Reiniciar el juego
    const resetGame = () => {
        gameState = Array(9).fill(null);
        currentPlayer = 'X';
        renderBoard();
    };

    resetButton.addEventListener('click', resetGame);
    renderBoard();
});
