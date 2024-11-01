<?php
require_once '../../config/db.php'; // Asegúrate de que la ruta sea correcta.
require_once '../Models/User.php'; // Ruta correcta hacia el modelo de usuario.

class UserController {
    public static function register() {
        $error = ""; // Variable para almacenar el error
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
            $user = new User();
    
            // Verificar si el nombre de usuario ya existe
            if ($user->userExists($username)) {
                $error = "El nombre de usuario ya está en uso. Por favor, elige otro.";
            } else {
                try {
                    $user->register($username, $password);
                    // Redirigir al login después de registrarse.
                    header('Location: ../Views/login.php');
                    exit();
                } catch (Exception $e) {
                    $error = "Error al registrar el usuario: " . $e->getMessage();
                }
            }
        }
    
        // Devolver el error al final del método (si hay alguno)
        return $error;
    }

    public static function login() {
        $error = "";  // Variable para almacenar el error
        
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
                $error = "Usuario o contraseña incorrectos";
            }
        }
    
        // Devolver el error al final del método (si hay alguno)
        return $error;
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
