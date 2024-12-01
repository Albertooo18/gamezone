document.addEventListener('DOMContentLoaded', () => {
    const player = document.getElementById('player');
    const gameArea = document.getElementById('game-area');
    const startButton = document.getElementById('start-game-button');
    const buttonContainer = document.querySelector('.button-container');
    const restartButton = document.createElement('button');
    let playerSpeed = 3;
    let playerScore = 0;
    let enemiesDestroyed = 0;
    let gameInterval;
    let enemyMovementIntervals = [];
    let gameEnded = false;
    let playerLives = 3;
    let bulletDamage = 10;
    let powerUpCounts = {
        speed: 0,
        bulletSpeed: 0,
        bulletDamage: 0
    };

    let canShoot = true;  // Variable para controlar si se puede disparar
    const shootInterval = 200;  // Intervalo entre disparos en milisegundos
    const MAX_POWERUP_STACK = 3;

    // Crear el contenedor de vidas
    const livesContainer = document.createElement('div');
    livesContainer.id = 'player-lives';
    livesContainer.textContent = `Vidas: ${playerLives}`;
    livesContainer.style.textAlign = 'center';
    livesContainer.style.fontSize = '1.2em';
    livesContainer.style.marginBottom = '10px';
    buttonContainer.parentElement.insertBefore(livesContainer, buttonContainer);

    // Configuración del botón de reinicio
    restartButton.textContent = 'Reiniciar Juego';
    restartButton.classList.add('game-button');
    restartButton.style.marginTop = '20px';
    restartButton.style.display = 'none';
    buttonContainer.appendChild(restartButton);

    // Iniciar juego con el botón de inicio
    startButton.addEventListener('click', () => {
        startButton.style.display = 'none';
        restartButton.style.display = 'none';
        startGame();
    });

    // Reiniciar el juego con el botón de reinicio
    restartButton.addEventListener('click', () => {
        resetGame();
        startButton.style.display = 'none';
        restartButton.style.display = 'none';
        startGame();
    });

    let keysPressed = {
        left: false,
        right: false,
        fire: false
    };

    // Eventos para actualizar el estado de las teclas presionadas
    document.addEventListener('keydown', (event) => {
        if (gameEnded) return;

        switch (event.key) {
            case 'ArrowLeft':
                keysPressed.left = true;
                break;
            case 'ArrowRight':
                keysPressed.right = true;
                break;
            case ' ':
                keysPressed.fire = true;
                break;
        }
    });

    document.addEventListener('keyup', (event) => {
        switch (event.key) {
            case 'ArrowLeft':
                keysPressed.left = false;
                break;
            case 'ArrowRight':
                keysPressed.right = false;
                break;
            case ' ':
                keysPressed.fire = false;
                break;
        }
    });

    // Función para actualizar el movimiento del jugador y disparar balas
    const updatePlayerMovement = () => {
        if (gameEnded) return;

        const playerRect = player.getBoundingClientRect();
        const gameAreaRect = gameArea.getBoundingClientRect();

        if (keysPressed.left) {
            if (playerRect.left <= gameAreaRect.left) {
                player.style.left = `${gameAreaRect.width - player.offsetWidth}px`; // Teletransporta al extremo derecho
            } else {
                player.style.left = `${player.offsetLeft - playerSpeed}px`;
            }
        }

        if (keysPressed.right) {
            if (playerRect.right >= gameAreaRect.right) {
                player.style.left = `0px`; // Teletransporta al extremo izquierdo
            } else {
                player.style.left = `${player.offsetLeft + playerSpeed}px`;
            }
        }

        if (keysPressed.fire) {
            fireBullet();
        }

        requestAnimationFrame(updatePlayerMovement); // Continúa el movimiento y disparo del jugador de manera suave
    };

    // Llama a `updatePlayerMovement()` para iniciar el bucle de actualización del jugador
    updatePlayerMovement();

    const fireBullet = () => {
        if (gameEnded || !canShoot) return;
    
        canShoot = false;  // Bloquea el disparo hasta que pase el intervalo
    
        const bullet = document.createElement('div');
        bullet.classList.add('bullet');
    
        // Añadir la bala al DOM primero para medir su ancho
        gameArea.appendChild(bullet);
    
        // Corregir la posición horizontal de la bala respecto al jugador
        bullet.style.left = `${player.offsetLeft + (player.offsetWidth / 2) - 25}px`;
    
        // Colocar la bala justo arriba del jugador
        bullet.style.bottom = `${player.offsetHeight+20}px`;
    
        bullet.dataset.damage = bulletDamage;
    
        moveBullet(bullet);
    
        // Permitir disparar de nuevo después del intervalo
        setTimeout(() => {
            canShoot = true;
        }, shootInterval);
    };

    // Mover bala hacia arriba
    const moveBullet = (bullet) => {
        let bulletInterval = setInterval(() => {
            if (gameEnded) {
                bullet.remove();
                clearInterval(bulletInterval);
                return;
            }

            bullet.style.bottom = `${parseInt(bullet.style.bottom) + bulletSpeed}px`;

            if (parseInt(bullet.style.bottom) > gameArea.offsetHeight) {
                bullet.remove();
                clearInterval(bulletInterval);
            } else {
                // Detectar colisión con enemigos
                const enemies = document.querySelectorAll('.enemy');
                enemies.forEach(enemy => {
                    if (detectCollision(bullet, enemy)) {
                        const damage = parseInt(bullet.dataset.damage);
                        const currentHealth = parseInt(enemy.dataset.health);
                        const newHealth = currentHealth - damage;

                        if (newHealth <= 0) {
                            // Guardar la posición del enemigo eliminado
                            const enemyX = enemy.offsetLeft;
                            const enemyY = enemy.offsetTop;

                            // Remover enemigo y bala al colisionar
                            enemy.remove();
                            bullet.remove();
                            clearInterval(bulletInterval);

                            // Actualizar el puntaje y enemigos eliminados
                            if (enemy.classList.contains('red')) {
                                playerScore += 10; // Los enemigos rojos otorgan 10 puntos
                            } else if (enemy.classList.contains('blue')) {
                                playerScore += 15; // Los enemigos azules otorgan 15 puntos
                            } else if (enemy.classList.contains('yellow')) {
                                playerScore += 20; // Los enemigos amarillos otorgan 20 puntos
                            }

                            enemiesDestroyed += 1;
                            updateScore();
                            updateEnemiesDestroyed();

                            // Llamar a la función para crear un power-up
                            createPowerUp(enemyX, enemyY);
                        } else {
                            enemy.dataset.health = newHealth;
                            bullet.remove();
                            clearInterval(bulletInterval);
                        }
                    }
                });
            }
        }, 20);
    };

    // Crear enemigo
    const createEnemy = () => {
        if (gameEnded) return;

        let enemyType;

        if (enemiesDestroyed >= 50) { // Después de 40 enemigos destruidos
            const randomValue = Math.random();
            if (randomValue < 0.33) {
                enemyType = 'red';
            } else if (randomValue < 0.66) {
                enemyType = 'blue';
            } else {
                enemyType = 'yellow';
            }
        } else if (enemiesDestroyed >= 20) { // Después de 20 enemigos destruidos
            enemyType = Math.random() < 0.5 ? 'red' : 'blue';
        } else { // Desde el inicio
            enemyType = 'red';
        }

        const enemy = document.createElement('div');
        enemy.classList.add('enemy', enemyType);
        enemy.style.left = `${Math.floor(Math.random() * (gameArea.offsetWidth - 50))}px`;
        enemy.style.top = '0px';

        // Asignar vida según el tipo de enemigo
        switch (enemyType) {
            case 'red':
                enemy.dataset.health = 20;
                break;
            case 'blue':
                enemy.dataset.health = 40;
                break;
            case 'yellow':
                enemy.dataset.health = 60;
                enemy.dataset.canDodge = 'true';
                break;
        }

        gameArea.appendChild(enemy);
        moveEnemy(enemy);
    };

    // Mover enemigo hacia abajo
    const moveEnemy = (enemy) => {
        let enemySpeed;

        if (enemy.classList.contains('red')) {
            enemySpeed = 1;
        } else {
            enemySpeed = 1.5;
        }

        let enemyInterval = setInterval(() => {
            if (gameEnded) {
                console.log("Game ended, clearing enemy interval.");
                clearInterval(enemyInterval);
                enemy.remove();
                return;
            }

            // Probabilidad de esquivar para enemigos amarillos
            if (enemy.classList.contains('yellow') && enemy.dataset.canDodge === 'true' && Math.random() < 0.1) {
                enemy.dataset.canDodge = 'false';

                // Calcular nueva posición para esquivar
                let newLeft = parseFloat(enemy.style.left) + (Math.random() < 0.5 ? -50 : 50);

                // Si el enemigo está cerca del borde izquierdo o derecho, pasa al otro lado
                if (newLeft < 0) {
                    newLeft = gameArea.offsetWidth - enemy.offsetWidth; // Se mueve al extremo derecho
                } else if (newLeft > gameArea.offsetWidth - enemy.offsetWidth) {
                    newLeft = 0; // Se mueve al extremo izquierdo
                }

                // Aplicar la nueva posición suavemente gracias a la propiedad CSS `transition`
                enemy.style.left = `${newLeft}px`;
            }

            enemy.style.top = `${parseFloat(enemy.style.top) + enemySpeed}px`;

            // Ajustar el límite según el tipo de enemigo
            let limit;
            if (enemy.classList.contains('red')) {
                limit = gameArea.offsetHeight - 260;
            } else if (enemy.classList.contains('blue')) {
                limit = gameArea.offsetHeight - 220; // Los enemigos azules pueden llegar más abajo
            } else if (enemy.classList.contains('yellow')) {
                limit = gameArea.offsetHeight - 220; // Los enemigos amarillos pueden llegar aún más abajo
            }

            if (parseFloat(enemy.style.top) > limit) {
                console.log(`Enemy at position ${enemy.style.top} reached the bottom and will be removed.`);
                clearInterval(enemyInterval);
                if (enemy.parentElement) {
                    enemy.remove();
                    playerLives -= 1;
                    updateLives();
                    if (playerLives <= 0) {
                        endGame();
                    }
                }
            }
        }, 15);
        enemyMovementIntervals.push(enemyInterval);
    };

    // Crear power-up
    const createPowerUp = (x, y) => {
        const powerUpType = getRandomPowerUpType();
        if (!powerUpType) return;

        const powerUp = document.createElement('div');
        powerUp.classList.add('item', powerUpType);
        powerUp.style.left = `${x}px`;
        powerUp.style.top = `${y}px`;
        gameArea.appendChild(powerUp);
        movePowerUp(powerUp, powerUpType);
    };

    const movePowerUp = (powerUp, powerUpType) => {
        let powerUpInterval = setInterval(() => {
            if (gameEnded) {
                clearInterval(powerUpInterval);
                powerUp.remove();
                return;
            }

            powerUp.style.top = `${parseFloat(powerUp.style.top) + 3.5}px`;  // Reducir la velocidad de caída para hacerla más lenta

            if (parseFloat(powerUp.style.top) > gameArea.offsetHeight) {
                powerUp.remove();
                clearInterval(powerUpInterval);
            } else if (detectCollision(powerUp, player)) {
                powerUp.remove();
                clearInterval(powerUpInterval);
                applyPowerUp(powerUpType);
            }
        }, 10);
    };

    // Obtener un tipo de power-up aleatorio
    const getRandomPowerUpType = () => {
        const availablePowerUps = Object.keys(powerUpCounts).filter(type => powerUpCounts[type] < MAX_POWERUP_STACK);
        const totalAvailablePowerUps = availablePowerUps.length;

        // 50% probabilidad de que no salga ningún power-up
        if (Math.random() < 0.80) {
            return null; // No cae power-up
        }

        // Si va a salir un power-up, elige uno de los disponibles
        if (totalAvailablePowerUps > 0) {
            return availablePowerUps[Math.floor(Math.random() * totalAvailablePowerUps)];
        }

        return null; // En caso de que todos los power-ups estén al máximo
    };

    // Aplicar el efecto del power-up
    const applyPowerUp = (type) => {
        powerUpCounts[type]++;
        switch (type) {
            case 'speed':
                playerSpeed += 0.8;
                break;
            case 'bulletSpeed':
                increaseBulletSpeed();
                shootInterval = shootInterval * 0.90;
                break;
            case 'bulletDamage':
                bulletDamage += 10;
                break;
        }
        updatePowerUps();
    };

    // Incrementar la velocidad de las balas
    let bulletSpeed = 10;
    const increaseBulletSpeed = () => {
        bulletSpeed += 2;
    };

    // Detectar colisión
    const detectCollision = (obj1, obj2) => {
        const rect1 = obj1.getBoundingClientRect();
        const rect2 = obj2.getBoundingClientRect();

        return !(
            rect1.right < rect2.left ||
            rect1.left > rect2.right ||
            rect1.bottom < rect2.top ||
            rect1.top > rect2.bottom
        );
    };

    // Actualizar puntaje
    const updateScore = () => {
        let scoreDisplay = document.getElementById('player-score');
        if (scoreDisplay) {
            scoreDisplay.textContent = `Puntaje: ${playerScore}`;
        }
    };

    // Actualizar enemigos eliminados
    const updateEnemiesDestroyed = () => {
        let enemiesDestroyedElement = document.getElementById('enemies-destroyed');
        if (enemiesDestroyedElement) {
            enemiesDestroyedElement.textContent = `Enemigos Eliminados: ${enemiesDestroyed}`;
        }
    };

    // Actualizar power-ups
    const updatePowerUps = () => {
        let powerUpsElement = document.getElementById('power-ups');
        if (powerUpsElement) {
            powerUpsElement.textContent = `Vel: x${powerUpCounts['speed']}, Vel disp: x${powerUpCounts['bulletSpeed']}, Daño: x${powerUpCounts['bulletDamage']}`;
        }
    };

    // Actualizar vidas
    const updateLives = () => {
        const livesContainer = document.getElementById('lives-container');
        livesContainer.innerHTML = ''; // Limpiar contenedor de vidas
    
        for (let i = 0; i < playerLives; i++) {
            const heart = document.createElement('div');
            heart.classList.add('life-heart');
            livesContainer.appendChild(heart);
        }
    };
    

    // Terminar el juego
    const endGame = () => {
        if (gameEnded) return;
        gameEnded = true;
        clearInterval(gameInterval);
        enemyMovementIntervals.forEach(interval => clearInterval(interval));
        enemyMovementIntervals = [];
        alert(`Juego Terminado. Tu puntaje final es: ${playerScore}`);
        restartButton.style.display = 'block';
    };

    // Reiniciar el juego
    const resetGame = () => {
        playerScore = 0;
        enemiesDestroyed = 0;
        gameEnded = false;
        playerSpeed = 5;
        bulletSpeed = 10;
        bulletDamage = 10;
        playerLives = 3;
        powerUpCounts = {
            speed: 0,
            bulletSpeed: 0,
            bulletDamage: 0
        };
        updateScore();
        updateEnemiesDestroyed();
        updatePowerUps();
        updateLives();

        // Eliminar todos los enemigos restantes
        const enemies = document.querySelectorAll('.enemy');
        enemies.forEach(enemy => enemy.remove());

        // Detener cualquier intervalo activo
        enemyMovementIntervals.forEach(interval => clearInterval(interval));
        enemyMovementIntervals = [];

        // Colocar al jugador en el centro de la pantalla
        player.style.left = `${(gameArea.offsetWidth - player.offsetWidth) / 2}px`;
    };

    // Iniciar el juego
    const startGame = () => {
        let enemySpawnInterval = 2400;
        gameInterval = setInterval(() => {
            createEnemy();

            if (enemiesDestroyed >= 20 && enemiesDestroyed < 40) {
                enemySpawnInterval *= 0.75; // Reducir el tiempo de aparición en un 15% después de desbloquear los enemigos azules
            } else if (enemiesDestroyed >= 40) {
                enemySpawnInterval *= 0.60; // Reducir el tiempo de aparición en un 15% cuando se desbloquean los enemigos amarillos
            }

            clearInterval(gameInterval);
            gameInterval = setInterval(createEnemy, enemySpawnInterval);
        }, enemySpawnInterval);
    };

    // Inicializar el puntaje y enemigos eliminados
    updateScore();
    updateEnemiesDestroyed();
    updatePowerUps();
    updateLives();
});
