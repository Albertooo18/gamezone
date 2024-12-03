<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../src/Views/login.php");
    exit();
}
$userId = $_SESSION['user_id'];
?>
<script>
const userId = <?php echo json_encode($userId); ?>;
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skater Game</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: black;
        }
        canvas {
            display: block;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <canvas id="cityCanvas"></canvas>

    <audio id="gameMusic" loop>
        <source src="skatee.mp3"> <!-- Asegúrate de que la ruta del archivo sea correcta -->
        Your browser does not support the audio element.
    </audio>

    <script>
        const canvas = document.getElementById('cityCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const buildings = [];
        const cars = [];
        let skater = {
            x: canvas.width / 4,
            y: canvas.height - 85,
            width: 40,
            height: 20,
            color: '#00FF00',
            speed: 5,
            wheelColor: '#000'
        };

        let gameOver = false;
        let score = 0;
        let elapsedSeconds = 0;
        let finalScore = 0;  // Variable para almacenar la puntuación final
        const motivationalMessages = [
            "¡Vas muy bien!",
            "¡Genial!",
            "¡Sigue así!",
            "¡Impresionante!",
            "¡Qué habilidad!",
            "¡No pares!"
        ];

        let messageToShow = "";
        let messageTimer = 0;

        const gameMusic = document.getElementById('gameMusic');

        // Función para reproducir la música al hacer clic
        function playMusic() {
            if (gameMusic.paused) {
                gameMusic.play().catch(error => {
                    console.error('Error al reproducir la música:', error);
                });
            }
        }

        // Reproducir música cuando el usuario hace clic
        document.body.addEventListener('click', playMusic);

        // Función de inicialización de edificios, coches y otros elementos sigue igual

        function initBuildings() {
            buildings.length = 0;
            for (let i = 0; i < 10; i++) {
                const width = 100 + Math.random() * 100;
                const height = 300 + Math.random() * 400;
                const x = i * 200 + 50;
                buildings.push({ x, width, height, color: getRandomNeonColor() });
            }
        }

        function initCars() {
            cars.length = 0;
            const roadY = canvas.height - 200; // Ajusté la altura de la calle
            const laneHeight = 60;  // Definir la altura uniforme de los carriles
            for (let i = 0; i < 3; i++) { // Tres carriles
                const y = roadY + i * laneHeight;
                const speed = 3 + Math.random() * 2;
                cars.push({
                    x: Math.random() * canvas.width + canvas.width,
                    y,
                    width: 60,
                    height: 30,
                    color: getRandomNeonColor(),
                    windowColor: '#FFFFFF',
                    speed: -speed
                });
            }
        }

        function getRandomNeonColor() {
            const neonColors = ['#FF007F', '#00FFDD', '#FFDD00', '#7700FF', '#00FF99'];
            return neonColors[Math.floor(Math.random() * neonColors.length)];
        }

        function drawBuildings() {
            buildings.forEach(building => {
                const gradient = ctx.createLinearGradient(
                    building.x,
                    canvas.height - building.height,
                    building.x + building.width,
                    canvas.height
                );
                gradient.addColorStop(0, building.color);
                gradient.addColorStop(1, '#000000');

                ctx.fillStyle = gradient;
                ctx.fillRect(building.x, canvas.height - building.height, building.width, building.height);

                ctx.strokeStyle = building.color;
                ctx.lineWidth = 2;
                ctx.strokeRect(building.x, canvas.height - building.height, building.width, building.height);
            });
        }

        // Función para dibujar la carretera con 3 carriles horizontales y líneas divisorias horizontales
        function drawRoad() {
            const roadHeight = 200; // Aumentar la altura de la calle
            ctx.fillStyle = '#222';
            ctx.fillRect(0, canvas.height - roadHeight, canvas.width, roadHeight);

            const laneHeight = 60;  // Altura de cada carril
            const roadY = canvas.height - roadHeight;

            // Dibujar los tres carriles
            ctx.fillStyle = '#666'; // Color de los carriles
            ctx.fillRect(0, roadY, canvas.width, laneHeight);  // Carril 1
            ctx.fillRect(0, roadY + laneHeight, canvas.width, laneHeight);  // Carril 2
            ctx.fillRect(0, roadY + laneHeight * 2, canvas.width, laneHeight);  // Carril 3

            // Dibujar las líneas divisorias horizontales entre los carriles
            ctx.fillStyle = '#FFDD00'; // Línea divisoria amarilla
            ctx.fillRect(0, roadY + laneHeight - 5, canvas.width, 10);  // Línea divisoria 1
            ctx.fillRect(0, roadY + laneHeight * 2 - 5, canvas.width, 10);  // Línea divisoria 2
        }

        function drawCars() {
            cars.forEach(car => {
                ctx.fillStyle = car.color;
                ctx.fillRect(car.x, car.y, car.width, car.height);

                ctx.fillStyle = car.windowColor;
                ctx.fillRect(car.x + 10, car.y + 5, car.width - 20, car.height / 2);

                ctx.fillStyle = 'black';
                ctx.beginPath();
                ctx.arc(car.x + 10, car.y + car.height, 7, 0, Math.PI * 2);
                ctx.arc(car.x + car.width - 10, car.y + car.height, 7, 0, Math.PI * 2);
                ctx.fill();
            });
        }

        function updateCars() {
            cars.forEach(car => {
                car.x += car.speed;
                if (car.x + car.width < 0) car.x = canvas.width + Math.random() * 200;
            });
        }

        function drawSkater() {
            ctx.fillStyle = skater.color;
            ctx.fillRect(skater.x, skater.y, skater.width, skater.height);

            ctx.fillStyle = skater.wheelColor;
            ctx.beginPath();
            ctx.arc(skater.x + 5, skater.y + skater.height + 5, 5, 0, Math.PI * 2);
            ctx.arc(skater.x + skater.width - 5, skater.y + skater.height + 5, 5, 0, Math.PI * 2);
            ctx.fill();
        }

        function updateSkater(keys) {
            const roadY = canvas.height - 200;
            const roadBottom = canvas.height - 20;
            if (keys.ArrowUp && skater.y > roadY) skater.y -= skater.speed;
            if (keys.ArrowDown && skater.y + skater.height < roadBottom) skater.y += skater.speed;
            if (keys.ArrowRight && skater.x + skater.width < canvas.width) skater.x += skater.speed;
            if (keys.ArrowLeft && skater.x > 0) skater.x -= skater.speed;
        }

        function checkCollisions() {
            cars.forEach(car => {
                if (
                    skater.x < car.x + car.width &&
                    skater.x + skater.width > car.x &&
                    skater.y < car.y + car.height &&
                    skater.y + skater.height > car.y
                ) {
                    gameOver = true;
                    finalScore = score;  // Al perder, se guarda la puntuación final
                }
            });
        }

        function handleGameOver() {
            gameMusic.pause();

            ctx.fillStyle = 'red';
            ctx.font = '50px Arial';
            ctx.fillText('Game Over!', canvas.width / 2 - 150, canvas.height / 2);
            ctx.fillStyle = 'white';
            ctx.font = '30px Arial';

            // Guardar la puntuación final al perder el juego
            saveScore(finalScore);

            Swal.fire({
                title: 'Game Over!',
                text: `Tu puntuación final: ${finalScore}\n¿Quieres seguir jugando o salir?`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Seguir jugando',
                cancelButtonText: 'Salir',
            }).then(result => {
                if (result.isConfirmed) {
                    restartGame();
                } else {
                    window.location.href = '../juegos.php';
                }
            });
        }

        function updateScore() {
            elapsedSeconds++;
            if (elapsedSeconds % 10 === 0) {
                score += 10;
                messageToShow = motivationalMessages[Math.floor(Math.random() * motivationalMessages.length)];
                messageTimer = 2;
            }
            if (elapsedSeconds % 60 === 0) {
                score += 50;
            }
        }

        function drawScore() {
            ctx.fillStyle = 'white';
            ctx.font = '20px Arial';
            ctx.fillText(`Puntos: ${score}`, canvas.width - 150, 30);
            ctx.fillText(`Tiempo: ${elapsedSeconds}s`, canvas.width - 150, 60);

            if (messageTimer > 0) {
                ctx.fillText(messageToShow, canvas.width / 2 - 100, 100);
                messageTimer -= 1 / 60;
            }
        }

        const saveScore = (score) => {
            if (!userId || userId === 0) {
                console.error("No se puede enviar la puntuación sin un ID de usuario válido.");
                return;
            }

            console.log("Enviando puntuación:", score, "para el usuario:", userId);

            fetch('../../src/Controllers/SkateScoreController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'saveScore',
                    user_id: userId,
                    game_id: 3,  // ID del juego "Skate"
                    score: score
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Respuesta del servidor:", data);
                if (data.success) {
                    console.log("Puntuación guardada correctamente");
                } else {
                    console.error("Error al guardar la puntuación:", data.error);
                }
            })
            .catch(error => {
                console.error("Hubo un error en la petición:", error);
            });
        };

        function restartGame() {
            gameOver = false;
            score = 0;
            elapsedSeconds = 0;
            finalScore = 0;  // Reiniciar la puntuación final
            skater = {
                x: canvas.width / 4,
                y: canvas.height - 85,
                width: 40,
                height: 20,
                color: '#00FF00',
                speed: 5,
                wheelColor: '#000'
            };
            initBuildings();
            initCars();
            gameMusic.pause();
            gameMusic.currentTime = 0;
            document.body.addEventListener('click', playMusic);
            gameLoop();
        }

        const keys = {};
        window.addEventListener('keydown', e => {
            if (gameOver) {
                restartGame();
            } else {
                keys[e.key] = true;
            }
        });
        window.addEventListener('keyup', e => (keys[e.key] = false));

        function gameLoop() {
            if (gameOver) {
                handleGameOver();
                return;
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            checkCollisions();
            drawBuildings();
            drawRoad();
            updateCars();
            drawCars();
            updateSkater(keys);
            drawSkater();
            drawScore();
            requestAnimationFrame(gameLoop);
        }

        initBuildings();
        initCars();
        setInterval(updateScore, 1000);
        gameLoop();
    </script>
</body>
</html>
