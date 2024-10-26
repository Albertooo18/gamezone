<?php
require_once '../../config/db.php'; // Asegúrate de que la ruta sea correcta.
require_once '../Models/User.php'; // Ruta correcta hacia el modelo de usuario.

class UserController {
    public static function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $user = new User();
            try {
                $user->register($username, $password);
                // Redirigir al login después de registrarse.
                header('Location: ../Views/login.php');
                exit();
            } catch (Exception $e) {
                echo "Error al registrar el usuario: " . $e->getMessage();
            }
        }
    }

    public static function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = new User();
            if ($user->verifyCredentials($username, $password)) {
                session_start();
                $_SESSION['username'] = $username;
                header('Location: ../../public/index.php');
                exit();
            } else {
                echo "<p style='color: red;'>Usuario o contraseña incorrectos</p>";
            }
        }
    }
}

// Manejo de las acciones (simplificado)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'register') {
        UserController::register();
    } elseif ($action === 'login') {
        UserController::login();
    }
}
