<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Juego en Desarrollo</title>
    <link rel="stylesheet" href="../assets/css/dev.css">
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
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="../../src/Views/account.php">Cuenta</a></li>
                <?php else: ?>
                    <li><a href="../../src/Views/login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Contenido principal -->
    <div class="container">
        <!-- Contenedor del GIF -->
        <div class="gif-container">
            <img src="../assets/img/obrero.gif" alt="GIF en desarrollo">
        </div>
        <main>
            <h1>Juego en Desarrollo</h1>
            <div class="development-rectangle">
                <p>Juego en desarrollo, vuelva pronto.</p>
            </div>
        </main>
    </div>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>

    <?php
    // Comprobar si el usuario está logueado y si el mensaje de bienvenida ya fue mostrado
    if (isset($_SESSION['username']) && !isset($_SESSION['welcome_shown'])) {
        // Mostrar notificación de bienvenida y marcarla como mostrada
        echo "
        <div class='notification' id='notification'>
            ¡Bienvenido, " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . "!
        </div>
        ";

        // Establecer la variable de sesión para indicar que el mensaje ya se mostró
        $_SESSION['welcome_shown'] = true;
    }
    ?>
    <script>
    // Código JavaScript para mostrar y ocultar la notificación
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.getElementById('notification');
        if (notification) {
            // Mostrar la notificación
            notification.classList.add('show');

            // Ocultar la notificación después de 5 segundos
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }
    });
    </script>

</body>
</html>
