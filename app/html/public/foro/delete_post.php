<?php
session_start();

$host = 'localhost';
$dbname = 'gamezone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario es admin
    if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
        header('Location: ../foro.php');
        exit();
    }

    // Eliminar la publicación
    $post_id = $_POST['post_id'];

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();

    header('Location: ../foro.php');
    exit();

} catch (PDOException $e) {
    $error_message = 'Error al eliminar la publicación: ' . $e->getMessage();
}
?>
