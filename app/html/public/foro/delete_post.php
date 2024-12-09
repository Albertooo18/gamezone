<?php
session_start();

$host = '172.31.21.41';  // Cambia esto por la IP privada de la máquina donde está el contenedor MariaDB
$db = 'gamezone';
$user = 'user';  // Usuario configurado en docker-compose
$pass = 'password';  // Contraseña configurada en docker-compose

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario es admin
    if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
        header('Location: ../foro.php');
        exit();
    }

    // Eliminar la publicación usando el procedimiento almacenado
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    if (!$post_id) {
        die('ID de publicación inválido.');
    }

    $stmt = $pdo->prepare("CALL EliminarPost(:post_id)");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: ../foro.php');
    exit();

} catch (PDOException $e) {
    $error_message = 'Error al eliminar la publicación: ' . $e->getMessage();
}
?>
