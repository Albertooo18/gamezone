<?php
session_start();
require_once '../../src/models/User.php';
require_once '../../src/models/Score.php'; // Requerir el modelo de Score

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: ../../src/Views/login.php");
    exit();
}

// Obtener información del usuario actual
$userModel = new User();
$userInfo = $userModel->getUserInfo($_SESSION['username']);

// Verificar si hay un error almacenado en la sesión para mostrar
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
unset($_SESSION['error']);

// Obtener las puntuaciones del usuario
$scoreModel = new Score();
$userScores = $scoreModel->getUserScores($userInfo['id']);
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
                <li><a href="../../index.php">Inicio</a></li>
                <li><a href="../../public/juegos.php">Juegos</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="account.php">Cuenta</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Columna 1: Foto de perfil -->
        <div class="column-1">
            <img class="profile-picture" src="<?php echo ($userInfo['profile_picture'] !== 'default-profile.webp') ? $userInfo['profile_picture'] : '../../public/assets/img/default-profile.webp'; ?>" alt="Foto de Perfil">
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
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
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userInfo['username']); ?>" disabled>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userInfo['email']); ?>">

                <label for="new-username">Cambiar Nombre de Usuario:</label>
                <input type="text" id="new-username" name="new-username">

                <label for="password">Nueva Contraseña:</label>
                <input type="password" id="password" name="password">

                <label for="current-password">Contraseña Actual (para confirmar cambios):</label>
                <input type="password" id="current-password" name="current-password" required>

                <button type="submit" class="save-changes-btn">Guardar Cambios</button>
            </form>
        </div>

        <!-- Columna 3: Puntuaciones Recientes -->
        <div class="column-3">
            <h2>Puntuaciones Recientes</h2>
            <?php if (!empty($userScores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Juego</th>
                            <th>Puntuación</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userScores as $score): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($score['game']); ?></td>
                                <td><?php echo htmlspecialchars($score['score']); ?></td>
                                <td><?php echo isset($score['created_at']) ? htmlspecialchars($score['created_at']) : 'No disponible'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tienes puntuaciones registradas.</p>
            <?php endif; ?>
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
