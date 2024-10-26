<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - GameZone</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css">
</head>
<body>
    <!-- Contenedor para el logo (con enlace a la página principal) -->
    <div class="logo-container">
        <a href="../../public/index.php">
            <img src="../../public/assets/img/nombre.png" alt="GameZone Logo">
        </a>
    </div>

    <!-- Header con el menú de navegación -->
    <header>
        <nav>
            <ul>
                <li><a href="../../public/index.php">Inicio</a></li>
                <li><a href="../../public/juegos.php">Juegos</a></li>
                <li><a href="../../public/contacto.php">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <!-- Formulario de registro -->
    <div class="auth-container">
        <h1>Registro de Usuario</h1>
        <form action="../../src/Controllers/UserController.php?action=register" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-primary">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
