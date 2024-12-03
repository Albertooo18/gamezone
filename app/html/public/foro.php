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

    // Número de publicaciones por página
    $posts_per_page = 30;

    // Obtén el número de página actual
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $posts_per_page;

    // Obtener publicaciones ordenadas por fecha (limitadas a 30 por página)
    $stmt = $pdo->prepare("SELECT p.id, p.text, p.image, u.username, p.likes, p.created_at 
                           FROM posts p
                           JOIN users u ON p.user_id = u.id
                           ORDER BY p.created_at DESC 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $posts_per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el número total de publicaciones
    $total_posts_stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $total_posts = $total_posts_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);

} catch (PDOException $e) {
    $error_message = 'Error al obtener las publicaciones: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone - Foro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">  <!-- Añadimos Bootstrap -->
    <link rel="stylesheet" href="assets/css/foro.css">  <!-- Ajusta la ruta del archivo CSS --> 
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
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Formulario para publicar (solo si el usuario está logeado) -->
        <?php if (isset($_SESSION['username'])): ?>
            <form action="foro/publish_post.php" method="POST" enctype="multipart/form-data" class="form-container">
                <!-- Texto de la publicación -->
                <textarea name="text" placeholder="Escribe tu publicación..." required class="form-control"></textarea>

                <!-- Input para archivo -->
                <div class="custom-file-upload">
                    <label for="file-upload" class="custom-file-upload-label">
                        <span>Subir Imagen</span>
                    </label>
                    <input id="file-upload" type="file" name="image" accept="image/*" class="form-control-file">
                    <span id="file-selected-name">No file selected</span>
                </div>

                <!-- Botón para publicar -->
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
                            <button type="submit">Dar Like</button>
                        </form>
                    <?php endif; ?>

                    <!-- Si eres admin, mostrar opción para eliminar -->
                    <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin'): ?>
                        <form action="foro/delete_post.php" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" onclick="return confirm('¿Estás seguro de eliminar esta publicación?');">Eliminar</button>
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
    </main>

    <footer>
        <p>&copy; 2024 GameZone. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Tu script personalizado -->
    <script>
        // Función para mostrar la fecha de manera amigable
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            const weeks = Math.floor(days / 7);
            const months = Math.floor(days / 30);

            if (seconds < 60) {
                return "Hace un momento";
            } else if (minutes < 60) {
                return `Hace ${minutes} minuto${minutes !== 1 ? "s" : ""}`;
            } else if (hours < 24) {
                return `Hace ${hours} hora${hours !== 1 ? "s" : ""}`;
            } else if (days < 7) {
                return `Hace ${days} día${days !== 1 ? "s" : ""}`;
            } else if (weeks < 4) {
                return `Hace ${weeks} semana${weeks !== 1 ? "s" : ""}`;
            } else if (months < 12) {
                return `Hace ${months} mes${months !== 1 ? "es" : ""}`;
            } else {
                return `Hace más de un año`;
            }
        }

        // Actualizar la fecha en cada publicación
        document.querySelectorAll('.post-time').forEach(function(element) {
            const time = element.getAttribute('data-time');
            element.textContent = formatTimeAgo(time);
        });

        // Función para actualizar el nombre del archivo seleccionado
        document.getElementById('file-upload').addEventListener('change', function() {
            const fileName = this.files.length > 0 ? this.files[0].name : 'No file selected';
            document.getElementById('file-selected-name').textContent = fileName;
        });
    </script>
</body>
</html>
