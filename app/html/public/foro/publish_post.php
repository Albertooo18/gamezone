<?php
session_start();

$host = 'localhost';
$dbname = 'gamezone';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el usuario está logeado
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    // Obtener el texto de la publicación
    $text = $_POST['text'];
    $user_id = $_SESSION['user_id'];

    // Subir la imagen si se ha seleccionado una
    $image = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Insertar la publicación en la base de datos
    $stmt = $pdo->prepare("INSERT INTO posts (text, image, user_id) VALUES (:text, :image, :user_id)");
    $stmt->bindParam(':text', $text);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header('Location: ../foro.php');
    exit();

} catch (PDOException $e) {
    $error_message = 'Error al guardar la publicación: ' . $e->getMessage();
}
?>
