<?php
session_start();

// Configuración de la conexión a la base de datos
$host = 'localhost';  // Cambiar por el nombre o IP del host de la base de datos
$dbname = 'gamezone'; // Nombre de la base de datos
$username = 'root';   // Usuario predeterminado de XAMPP
$password = '';       // Contraseña predeterminada de XAMPP

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si el formulario de eliminación es enviado
    if (isset($_POST['delete_score'])) {
        $score_id = $_POST['score_id'];
        
        // Verificar si el usuario es el admin
        if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin') {
            // Eliminar el registro de la tabla
            $delete_stmt = $pdo->prepare("DELETE FROM scores WHERE id = :score_id");
            $delete_stmt->bindParam(':score_id', $score_id, PDO::PARAM_INT);
            $delete_stmt->execute();
        }
    }

    // Obtener las puntuaciones ordenadas de mayor a menor, junto con el nombre del juego
    $stmt = $pdo->query("SELECT s.id, s.score, u.username, g.name AS game_name
                         FROM scores s
                         JOIN users u ON s.user_id = u.id
                         JOIN games g ON s.game_id = g.id
                         ORDER BY s.score DESC
                         LIMIT 10");

    // Obtener los resultados de la consulta
    $leaderboards = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si hay un error en la conexión o consulta, muestra un mensaje de error
    $error_message = 'Error al obtener las puntuaciones: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Leaderboard</title>
    <link rel="stylesheet" href="assets/css/leaderboard.css">  <!-- Ajusta la ruta del archivo CSS -->
</head>
<body>

    <div class="logo-container">
        <img src="assets/img/logo.png" alt="GameZone Logo">
    </div>

    <!-- Header con la navegación (sin cambios) -->
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="juegos.php">Juegos</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="../src/Views/account.php">Cuenta</a></li>
                <?php else: ?>
                    <li><a href="../src/Views/login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Contenido principal de la página -->
    <main class="leaderboard-section">
        <h2>Top 10 Puntuaciones</h2>

        <!-- Mostrar mensaje de error si hubo un problema al obtener las puntuaciones -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Tabla de resultados -->
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Puntuación</th>
                    <th>Juego</th> <!-- Nueva columna para el juego -->
                    <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin'): ?>
                        <th>Acciones</th> <!-- Columna de acciones para el admin -->
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($leaderboards) && !empty($leaderboards)): ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($leaderboards as $entry): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($entry['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $entry['score']; ?></td>
                            <td><?php echo htmlspecialchars($entry['game_name'], ENT_QUOTES, 'UTF-8'); ?></td> <!-- Nombre del juego -->
                            
                            <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin'): ?>
                                <!-- Formulario de eliminación -->
                                <td>
                                    <form action="leaderboard.php" method="POST">
                                        <input type="hidden" name="score_id" value="<?php echo $entry['id']; ?>">
                                        <button type="submit" name="delete_score" onclick="return confirm('¿Estás seguro de eliminar esta puntuación?');">Eliminar</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay puntuaciones disponibles.</td> <!-- Aquí se cambió a 5 columnas -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
