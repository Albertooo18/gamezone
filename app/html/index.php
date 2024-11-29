<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Catálogo de Juegos</title>
    <link rel="stylesheet" href="public/assets/css/index.css">
</head>
<body>
    <!-- Contenedor para el logo -->
    <div class="logo-container">
        <img src="public/assets/img/logo.png" alt="GameZone Logo">
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="public/juegos.php">Juegos</a></li>
                <li><a href="public/contactos.php">Contacto</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="src/Views/account.php">Cuenta</a></li>
                <?php else: ?>
                    <li><a href="src/Views/login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>


    <!-- Contenedor principal con aside y edificios -->
    <div class="main-container">
        <!-- Barra lateral (aside) -->
        <aside>
            <h2>Enlaces útiles</h2>
            <ul>
                <li><a href="#">Noticias</a></li>
                <li><a href="#">Top Juegos</a></li>
                <li><a href="#">Foros</a></li>
            </ul>
        </aside>

        <!-- Sección de edificios (main content) -->
        <section class="building-section">
            <div class="building">
                <a href="dev.php">
                    <img class="building-img" src="../html/public/assets/img/jungle.webp" alt="Juego Selva">
                </a>
                <div class="building-info">
                    <h3>Juego Selva</h3>
                    <p>Explora la jungla mientras esquivas obstáculos y encuentras tesoros ocultos.</p>
                </div>
            </div>
            <div class="building">
                <a href="dev.php">
                    <img class="building-img" src="../html/public/assets/img/racing.webp" alt="Juego Carrera">
                </a>
                <div class="building-info">
                    <h3>Juego Carrera</h3>
                    <p>Conduce a toda velocidad por la ciudad y evita el tráfico para llegar primero a la meta.</p>
                </div>
            </div>
            <div class="building">
                <a href="dev.php">
                    <img class="building-img" src="../html/public/assets/img/space.webp" alt="Juego Espacial">
                </a>
                <div class="building-info">
                    <h3>Juego Espacial</h3>
                    <p>Explora el universo y enfréntate a enemigos en emocionantes batallas espaciales.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>

    <?php
    if (isset($_SESSION['username']) && !isset($_SESSION['welcome_shown'])) {
        echo "
        <div class='notification' id='notification'>
            ¡Bienvenido, " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . "!
        </div>
        ";
        $_SESSION['welcome_shown'] = true;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }
    });
    </script>

</body>
</html>
