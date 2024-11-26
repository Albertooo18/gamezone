<?php
require_once '../../config/db.php'; // Conexión a la base de datos

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function register($username, $email, $password, $profile_picture = 'default-profile.webp') {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $profile_picture]);
    }

    public function verifyCredentials($username, $password) {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function userExists($username) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

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

    public function getUserInfo($username) {
        $stmt = $this->pdo->prepare("SELECT username, email, profile_picture FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function updateProfilePicture($username, $profile_picture) {
        $stmt = $this->pdo->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
        $stmt->execute([$profile_picture, $username]);
    }
}