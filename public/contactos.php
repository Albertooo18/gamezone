<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - GameZone</title>
    <link rel="stylesheet" href="assets/css/contacto.css">
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
                <li><a href="contactos.php">Contacto</a></li>
                <li><a href="../src/Views/login.php">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="contacto-titulo">Contacto</h1>
        <p class="contacto-descripcion">Puedes contactarnos a través del siguiente formulario:</p>
        <form class="contacto-formulario">
            <label class="form-label" for="nombre">Nombre:</label>
            <input class="form-input" type="text" id="nombre" name="nombre" required><br><br>
            <label class="form-label" for="email">Correo Electrónico:</label>
            <input class="form-input" type="email" id="email" name="email" required><br><br>
            <label class="form-label" for="mensaje">Mensaje:</label><br>
            <textarea class="form-textarea" id="mensaje" name="mensaje" rows="4" required></textarea><br><br>
            <button class="btn-enviar" type="submit">Enviar</button>
        </form>
    </main>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
