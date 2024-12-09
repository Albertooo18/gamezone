<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'gamezone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Función para verificar si el usuario es admin
    function isAdmin($pdo, $username) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['role'] === 'admin';
    }

    // Verificar si el usuario actual es admin
    $isAdmin = false;
    if (isset($_SESSION['username'])) {
        $isAdmin = isAdmin($pdo, $_SESSION['username']);
    }

    // Número de publicaciones por página
    $posts_per_page = 30;

    // Obtén el número de página actual
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $posts_per_page;

    // Obtener publicaciones y verificar si el usuario ha dado like a cada publicación
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // ID del usuario logueado
    $stmt = $pdo->prepare("
        SELECT p.id, p.text, p.image, u.username, p.likes, p.created_at,
            EXISTS(
                SELECT 1 FROM post_likes pl WHERE pl.post_id = p.id AND pl.user_id = :user_id
            ) AS user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $posts_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el número total de publicaciones
    $total_posts_stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $total_posts = $total_posts_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);

    // Si se envía el formulario para eliminar una publicación
    if (isset($_POST['delete_post']) && $isAdmin) {
        $post_id = $_POST['post_id'];

        $delete_stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
        $delete_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $delete_stmt->execute();
    }

} catch (PDOException $e) {
    // Mostrar mensaje de error amigable
    $error_message = "No se puede conectar a la base de datos. Por favor, inténtalo más tarde.";
    $pdo = null; // Destruir conexión si falló
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Foro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/foro.css">
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

    <main>
        <h2>Foro de GameZone</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php else: ?>
            <!-- Formulario para publicar (solo si el usuario está logeado) -->
            <?php if (isset($_SESSION['username'])): ?>
                <form action="foro/publish_post.php" method="POST" enctype="multipart/form-data" class="form-container">
                    <textarea name="text" placeholder="Escribe tu publicación..." required class="form-control"></textarea>
                    <div class="custom-file-upload">
                        <label for="file-upload" class="custom-file-upload-label">
                            <span>Subir Imagen</span>
                        </label>
                        <input id="file-upload" type="file" name="image" accept="image/*" class="form-control-file">
                        <span id="file-selected-name">No file selected</span>
                    </div>
                    <button class="btn btn-primary custom-submit-btn" type="submit">Publicar</button>
                </form>
            <?php endif; ?>

            <!-- Mostrar publicaciones -->
            <div class="posts">
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <h3><?php echo htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>
                        <?php if ($post['image']): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Imagen de la publicación">
                        <?php endif; ?>
                        <p>Likes: <?php echo $post['likes']; ?> | <span class="post-time" data-time="<?php echo $post['created_at']; ?>"><?php echo $post['created_at']; ?></span></p>

                        <!-- Like button -->
                        <?php if (isset($_SESSION['username'])): ?>
                            <form action="foro/like_post.php" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit">
                                    <?php echo $post['user_liked'] ? 'Quitar Like' : 'Dar Like'; ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <!-- Si eres admin, mostrar opción para eliminar -->
                        <?php if ($isAdmin): ?>
                            <form action="foro.php" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="delete_post" onclick="return confirm('¿Estás seguro de eliminar esta publicación?');">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
