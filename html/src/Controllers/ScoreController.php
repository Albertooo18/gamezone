<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/Score.php';

class ScoreController {
    public static function saveScore($userId, $gameId, $score) {
        // Verificar que los valores sean válidos
        if ($userId === null || $gameId === null || !is_numeric($score)) {
            throw new InvalidArgumentException("Datos inválidos para guardar la puntuación");
        }
        
        // Conectar a la base de datos
        $db = Db::getConnection();

        // Verificar si ya existe un registro para este usuario y juego
        $query = "SELECT * FROM scores WHERE user_id = :user_id AND game_id = :game_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        $existingScore = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingScore) {
            // Si ya existe, actualizar la puntuación si es mayor
            $query = "UPDATE scores SET score = GREATEST(score, :score), created_at = NOW() WHERE user_id = :user_id AND game_id = :game_id";
        } else {
            // Si no existe, insertar nueva puntuación
            $query = "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (:user_id, :game_id, :score, NOW())";
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function getHighScores() {
        $scoreModel = new Score();
        return $scoreModel->getHighScores();
    }

    public static function getUserHighScore($userId) {
        $scoreModel = new Score();
        return $scoreModel->getUserHighScore($userId);
    }
}

// Manejo de peticiones POST desde JavaScript (fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'saveScore') {
        $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
        $score = isset($_POST['score']) ? $_POST['score'] : null;

        if ($userId !== null && $score !== null) {
            try {
                $gameId = 1; // ID del juego "3 en Raya" (puedes ajustarlo según tu base de datos)
                ScoreController::saveScore($userId, $gameId, $score);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos insuficientes para guardar la puntuación']);
        }
    }
}
?>