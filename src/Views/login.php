<?php
require_once '../../src/Controllers/UserController.php';

// Iniciar la variable de error como vacía
$error = "";

// Llamar a la función de login para manejar la solicitud si se hace un POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = UserController::login();  // Ejecuta el login y almacena el error si existe
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameZone</title>
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
                <li><a href="index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <div class="auth-container">
        <h1>Iniciar Sesión</h1>
        <!-- Formulario de login -->
        <form action="login.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-primary">Ingresar</button>
        </form>

        <!-- Mostrar el mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
