<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Meta viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Catálogo de Juegos</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
        <li><a href="contacto.php">Contacto</a></li>
        <li><a href="login.php">Iniciar Sesión</a></li> <!-- Nuevo enlace -->
    </ul>
</nav>
    </header>

    <!-- Contenido principal -->
    <div class="container">
        <!-- Barra lateral (aside) -->
        <aside>
            <h2>Enlaces Útiles</h2>
            <ul>
                <li><a href="#">Noticias</a></li>
                <li><a href="#">Top Juegos</a></li>
                <li><a href="#">Foros</a></li>
            </ul>
        </aside>

        <!-- Galería de juegos (main content) -->
        <main>
            <h1>Catálogo de Juegos</h1>
            <div class="galeria">
                <img src="assets/img/cook.webp" alt="Juego 1">
                <img src="assets/img/jungle.webp" alt="Juego 2">
                <img src="assets/img/racing.webp" alt="Juego 3">
                <img src="assets/img/robot.webp" alt="Juego 4">
                <img src="assets/img/skate.webp" alt="Juego 5">
                <img src="assets/img/space.webp" alt="Juego 6">
                <img src="assets/img/tictactoe.webp" alt="Juego 7">
                <img src="assets/img/underwater.webp" alt="Juego 8">
            </div>
        </main>
    </div>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>

    <?php
    session_start();

    // Comprobar si el usuario está logueado y si el mensaje de bienvenida ya fue mostrado
    if (isset($_SESSION['username']) && !isset($_SESSION['welcome_shown'])) {
        // Mostrar notificación de bienvenida y marcarla como mostrada
        echo "
        <div class='notification' id='notification'>
            ¡Bienvenido, " . $_SESSION['username'] . "!
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
