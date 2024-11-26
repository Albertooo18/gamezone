<?php
require_once '../../config/db.php';
require_once '../Models/User.php';

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

        return $error;
    }

    public static function updateUserInfo() {
        session_start();
        $current_username = $_SESSION['username'];
        $new_username = $_POST['new-username'];
        $email = $_POST['email'];
        $current_password = $_POST['current-password'];
        $new_password = $_POST['password'];

        $user = new User();

        // Verificar la contraseña actual antes de hacer los cambios
        if (!$user->verifyCredentials($current_username, $current_password)) {
            $_SESSION['error'] = "Contraseña actual incorrecta";
            header("Location: ../../src/Views/account.php");
            exit();
        }

        // Actualizar información del usuario
        $hashed_new_password = !empty($new_password) ? password_hash($new_password, PASSWORD_BCRYPT) : null;
        $user->updateUserInfo($current_username, $new_username ?: $current_username, $email, $hashed_new_password);

        // Actualizar la sesión con el nuevo nombre de usuario si ha cambiado
        $_SESSION['username'] = !empty($new_username) ? $new_username : $current_username;

        // Redirigir de vuelta a la página de gestión de cuenta para reflejar los cambios
        header("Location: ../../src/Views/account.php");
        exit();
    }

    public static function updateProfilePicture() {
        session_start();
        $username = $_SESSION['username'];

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = uniqid('profile_', true) . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $destPath = '../../public/uploads/' . $fileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $user = new User();
                $user->updateProfilePicture($username, $destPath);

                header("Location: ../../src/Views/account.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al subir la imagen";
                header("Location: ../../src/Views/account.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "No se seleccionó ninguna imagen. Por favor, selecciona una.";
            header("Location: ../../src/Views/account.php");
            exit();
        }
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        header("Location: ../../public/index.php");
        exit();
    }
}

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
    } elseif ($action === 'updateProfilePicture') {
        UserController::updateProfilePicture();
    }
}
