<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Agregar encabezados CORS para evitar problemas si se está probando entre diferentes dominios
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

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

        // Obtener las puntuaciones existentes del usuario para este juego, ordenadas de mayor a menor
        $query = "SELECT * FROM scores WHERE user_id = :user_id AND game_id = :game_id ORDER BY score DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        $existingScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($existingScores) < 3) {
            // Si hay menos de 3 puntuaciones, insertar la nueva puntuación
            $query = "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (:user_id, :game_id, :score, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Si ya existen 3 puntuaciones, verificar si la nueva puntuación es mayor que la menor existente
            $lowestScore = $existingScores[2]['score'];
            if ($score > $lowestScore) {
                // Eliminar la puntuación más baja
                $query = "DELETE FROM scores WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $existingScores[2]['id'], PDO::PARAM_INT);
                $stmt->execute();

                // Insertar la nueva puntuación
                $query = "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (:user_id, :game_id, :score, NOW())";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
                $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
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
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? null;
    $score = $_POST['score'] ?? null;

    if ($action === 'saveScore' && $userId !== null && $score !== null) {
        try {
            $gameId = 1; // ID del juego "3 en Raya" (puedes ajustarlo según tu base de datos)
            ScoreController::saveScore($userId, $gameId, $score);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Parámetros insuficientes o inválidos']);
    }
}
?>
