<?php
session_start();

// Configuración de la conexión a la base de datos
$host = '172.31.24.224';  // Cambia esto por la IP privada de la máquina donde está el contenedor MariaDB
$db = 'gamezone';
$user = 'user';  // Usuario configurado en docker-compose
$pass = 'password';  // Contraseña configurada en docker-compose

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Función para verificar si el usuario es admin
    function isAdmin($pdo, $username) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['role'] === 'admin';
    }

    $isAdmin = false;
    if (isset($_SESSION['username'])) {
        $isAdmin = isAdmin($pdo, $_SESSION['username']);
    }

    // Si el formulario de eliminación es enviado
    if (isset($_POST['delete_score']) && $isAdmin) {
        $score_id = $_POST['score_id'];

        $delete_stmt = $pdo->prepare("DELETE FROM scores WHERE id = :score_id");
        $delete_stmt->bindParam(':score_id', $score_id, PDO::PARAM_INT);
        $delete_stmt->execute();
    }

    // Variables para la búsqueda
    $search_query = '';
    $leaderboards = [];

    // Si el formulario de búsqueda es enviado
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = trim($_GET['search']);
        $stmt = $pdo->prepare("
            SELECT s.id, s.score, u.username, g.name AS game_name
            FROM scores s
            JOIN users u ON s.user_id = u.id
            JOIN games g ON s.game_id = g.id
            WHERE u.username LIKE :search_query
               OR g.name LIKE :search_query
               OR DATE(s.created_at) = :search_date
            ORDER BY s.score DESC
            LIMIT 10
        ");

        // Parámetros para la búsqueda
        $stmt->execute([
            ':search_query' => '%' . $search_query . '%',
            ':search_date' => $search_query // En caso de búsqueda por fecha exacta
        ]);
        $leaderboards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Consulta predeterminada (sin búsqueda)
        $stmt = $pdo->query("
            SELECT s.id, s.score, u.username, g.name AS game_name
            FROM scores s
            JOIN users u ON s.user_id = u.id
            JOIN games g ON s.game_id = g.id
            ORDER BY s.score DESC
            LIMIT 10
        ");
        $leaderboards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error_message = 'Error al obtener las puntuaciones: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Leaderboard</title>
    <link rel="stylesheet" href="assets/css/leaderboard.css">
</head>
<body>

    <div class="logo-container">
        <img src="assets/img/logo.png" alt="GameZone Logo">
    </div>

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

    <main class="leaderboard-section">
        <h2>Top 10 Puntuaciones</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <!-- Formulario de búsqueda -->
            <form action="leaderboard.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Buscar por usuario, juego o fecha (YYYY-MM-DD)" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Buscar</button>
            </form>
        <?php endif; ?>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Puntuación</th>
                    <th>Juego</th>
                    <?php if ($isAdmin): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leaderboards)): ?>
                    <?php $rank = 1; ?>
                    <?php foreach ($leaderboards as $entry): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($entry['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $entry['score']; ?></td>
                            <td><?php echo htmlspecialchars($entry['game_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php if ($isAdmin): ?>
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
                        <td colspan="5">No hay puntuaciones disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
