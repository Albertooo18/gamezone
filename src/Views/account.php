<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cuenta - GameZone</title>
    <link rel="stylesheet" href="../../public/assets/css/account.css">
</head>
<body>
    <div class="logo-container">
        <img src="../../public/assets/img/logo.png" alt="GameZone Logo">
    </div>

    <header>
        <nav>
            <ul>
                <li><a href="../../public/index.php">Inicio</a></li>
                <li><a href="../../public/juegos.php">Juegos</a></li>
                <li><a href="../../public/contactos.php">Contacto</a></li>
                <li><a href="account.php">Cuenta</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Columna 1: Foto de perfil -->
        <div class="column-1">
            <img class="profile-picture" src="../../public/assets/img/default-profile.webp" alt="Foto de Perfil">
            <form action="../../src/Controllers/UserController.php?action=updateProfilePicture" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*">
                <button type="submit" class="change-picture-btn">Cambiar Imagen</button>
            </form>
        </div>

        <!-- Columna 2: Información de usuario -->
        <div class="column-2">
            <h2>Información de Usuario</h2>
            <form action="../../src/Controllers/UserController.php?action=updateUserInfo" method="POST">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="NombreActual" disabled>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="CorreoActual@example.com">

                <label for="new-username">Cambiar Nombre de Usuario:</label>
                <input type="text" id="new-username" name="new-username">

                <label for="password">Nueva Contraseña:</label>
                <input type="password" id="password" name="password">

                <label for="current-password">Contraseña Actual (para confirmar cambios):</label>
                <input type="password" id="current-password" name="current-password" required>

                <button type="submit" class="save-changes-btn">Guardar Cambios</button>
            </form>
        </div>
    </main>

    <!-- Botón de Cerrar Sesión -->
    <div class="logout-container">
        <form action="../../src/Controllers/UserController.php?action=logout" method="POST">
            <button type="submit" class="logout-btn">Cerrar Sesión</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados. My last year being broke.</p>
    </footer>
</body>
</html>
