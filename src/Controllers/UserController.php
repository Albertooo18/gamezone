<?php
require_once '../../config/db.php'; // Conexión a la base de datos
require_once '../Models/User.php'; // Ruta correcta hacia el modelo de usuario

class UserController {
    public static function register() {
        $error = ""; // Variable para almacenar el error
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm-password'];

            // Validar si las contraseñas coinciden
            if ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden. Por favor, intenta nuevamente.";
                return $error;
            }

            // Validar el formato del email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El formato del email no es válido. Por favor, ingresa un email correcto.";
                return $error;
            }

            // Hash de la contraseña
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
            $user = new User();
    
            // Verificar si el nombre de usuario o el email ya existen
            if ($user->userExists($username)) {
                $error = "El nombre de usuario ya está en uso. Por favor, elige otro.";
            } elseif ($user->emailExists($email)) {
                $error = "El email ya está en uso. Por favor, elige otro.";
            } else {
                try {
                    $user->register($username, $email, $hashed_password);
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

    public static function updateUserInfo() {
        $error = ""; // Variable para almacenar el error
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $current_username = $_SESSION['username'];
            $new_username = $_POST['new-username'];
            $email = $_POST['email'];
            $current_password = $_POST['current-password'];
            $new_password = $_POST['password'];

            $user = new User();

            // Verificar la contraseña actual antes de hacer los cambios
            if (!$user->verifyCredentials($current_username, $current_password)) {
                $error = "La contraseña actual es incorrecta. No se pueden realizar los cambios.";
                return $error;
            }

            // Verificar si el nuevo nombre de usuario o email ya existen
            if (!empty($new_username) && $new_username !== $current_username && $user->userExists($new_username)) {
                $error = "El nuevo nombre de usuario ya está en uso. Por favor, elige otro.";
                return $error;
            }

            if (!empty($email) && $email !== $user->getUserInfo($current_username)['email'] && $user->emailExists($email)) {
                $error = "El nuevo email ya está en uso. Por favor, elige otro.";
                return $error;
            }

            try {
                // Actualizar la información del usuario (nombre de usuario, email y/o contraseña)
                $hashed_new_password = !empty($new_password) ? password_hash($new_password, PASSWORD_BCRYPT) : null;
                $user->updateUserInfo($current_username, $new_username, $email, $hashed_new_password);
                $_SESSION['username'] = $new_username ?: $current_username; // Actualizar el nombre de usuario en la sesión si ha cambiado
            } catch (Exception $e) {
                $error = "Error al actualizar la información del usuario: " . $e->getMessage();
            }
        }

        // Devolver el error al final del método (si hay alguno)
        return $error;
    }

    public static function logout() {
        // Iniciar la sesión si aún no se ha hecho
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        // Registro de depuración
        error_log("UserController::logout() llamado");
    
        // Eliminar todas las variables de sesión
        session_unset();
        // Destruir la sesión
        session_destroy();
        // Redirigir a la página principal
        header("Location: ../../public/index.php");
        exit();
    }
}

// Manejo de las acciones (simplificado)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'register') {
        UserController::register();
    } elseif ($action === 'login') {
        UserController::login();
    } elseif ($action === 'logout') {
        UserController::logout();
    } elseif ($action === 'updateUserInfo') {
        UserController::updateUserInfo();
    }
}
