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

    // Obtener el texto de la publicación
    $text = htmlspecialchars(trim($_POST['text']), ENT_QUOTES, 'UTF-8');
    $user_id = $_SESSION['user_id'];

    // Subir la imagen si se ha seleccionado una
    $image = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            die('Formato de imagen no permitido.');
        }

        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Insertar la publicación usando el procedimiento almacenado
    $stmt = $pdo->prepare("CALL InsertarPost(:text, :image, :user_id)");
    $stmt->bindParam(':text', $text, PDO::PARAM_STR);
    $stmt->bindParam(':image', $image, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: ../foro.php');
    exit();

} catch (PDOException $e) {
    $error_message = 'Error al guardar la publicación: ' . $e->getMessage();
}
?>
