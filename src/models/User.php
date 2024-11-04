<?php
require_once '../../config/db.php'; // Conexión a la base de datos

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Registrar usuario con email e imagen de perfil
    public function register($username, $email, $password, $profile_picture = 'default-profile.webp') {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $profile_picture]);
    }

    // Verificar credenciales del usuario (login)
    public function verifyCredentials($username, $password) {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    // Verificar si el usuario ya existe
    public function userExists($username) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
    
        return $count > 0; // Si el conteo es mayor a 0, el usuario ya existe
    }

    // Verificar si el email ya está registrado
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        return $count > 0; // Si el conteo es mayor a 0, el email ya está en uso
    }

    // Actualizar la información del usuario
    public function updateUserInfo($current_username, $new_username, $email, $new_password = null, $new_profile_picture = null) {
        $query = "UPDATE users SET username = ?, email = ?";
        $params = [$new_username, $email];

        if ($new_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $query .= ", password = ?";
            $params[] = $hashed_password;
        }

        if ($new_profile_picture) {
            $query .= ", profile_picture = ?";
            $params[] = $new_profile_picture;
        }

        $query .= " WHERE username = ?";
        $params[] = $current_username;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
    }

    // Obtener información del usuario por nombre de usuario
    public function getUserInfo($username) {
        $stmt = $this->pdo->prepare("SELECT username, email, profile_picture FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // Actualizar solo la imagen de perfil
    public function updateProfilePicture($username, $profile_picture) {
        $stmt = $this->pdo->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
        $stmt->execute([$profile_picture, $username]);
    }
}
