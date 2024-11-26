<?php
$host = 'localhost';
$db = 'gamezone';
$user = 'root';
$pass = ''; // Cambia si tienes una contraseña para tu servidor de MySQL.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
