<?php
session_start();

$host = '172.31.21.41';  // Cambia esto por la IP privada de la máquina donde está el contenedor MariaDB
$db = 'gamezone';
$user = 'user';  // Usuario configurado en docker-compose
$pass = 'password';  // Contraseña configurada en docker-compose

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario está logeado
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    // Obtener el id del usuario y la publicación
    $user_id = $_SESSION['user_id'];  // Suponiendo que el ID del usuario está guardado en la sesión
    $post_id = $_POST['post_id'];

    // Verificar si el usuario ya ha dado like a esta publicación
    $stmt = $pdo->prepare("SELECT * FROM post_likes WHERE user_id = :user_id AND post_id = :post_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();

    // Si el usuario ya ha dado like, lo elimina
    if ($stmt->rowCount() > 0) {
        // Eliminar el like de la base de datos
        $stmt = $pdo->prepare("DELETE FROM post_likes WHERE user_id = :user_id AND post_id = :post_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        // Decrementar el contador de likes en la tabla de publicaciones
        $stmt = $pdo->prepare("UPDATE posts SET likes = likes - 1 WHERE id = :post_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        // Redirigir de vuelta a la página del foro
        header('Location: ../foro.php');
        exit();
    }

    // Si el usuario no ha dado like, lo registra
    else {
        // Registrar el like en la tabla de likes
        $stmt = $pdo->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (:user_id, :post_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        // Incrementar el contador de likes en la tabla de publicaciones
        $stmt = $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE id = :post_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        // Redirigir de vuelta a la página del foro
        header('Location: ../foro.php');
        exit();
    }

} catch (PDOException $e) {
    $error_message = 'Error al dar/quitar like: ' . $e->getMessage();
    echo $error_message;  // Puedes agregar un manejo de errores más robusto aquí
}
?>
