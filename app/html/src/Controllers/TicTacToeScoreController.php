<?php
// Asegúrate de que la clase Db esté incluida
require_once __DIR__ . '/../../config/db.php'; 

class TicTacToeScoreController {
    
    // Método para guardar la puntuación
    public static function saveScore($userId, $gameId, $score) {
        if ($userId === null || $gameId === null || !is_numeric($score)) {
            return ['success' => false, 'error' => 'Datos inválidos para guardar la puntuación'];
        }

        // Conectar a la base de datos
        $db = Db::getConnection();

        // Obtener las puntuaciones existentes del usuario para este juego
        $query = "SELECT * FROM scores WHERE user_id = :user_id AND game_id = :game_id ORDER BY score DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        $existingScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay menos de 3 puntuaciones
        if (count($existingScores) < 3) {
            // Insertar la nueva puntuación
            $query = "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (:user_id, :game_id, :score, NOW())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Si ya hay 3 puntuaciones, eliminar la más baja si es necesario
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

        return ['success' => true];
    }

    // Método para obtener la puntuación más alta de un usuario
    public static function getUserHighScore($userId) {
        // Conectar a la base de datos
        $db = Db::getConnection();

        // Obtener la puntuación más alta del usuario
        $query = "SELECT MAX(score) as high_score FROM scores WHERE user_id = :user_id AND game_id = 1";  // Asegúrate de que `game_id = 1` es para TicTacToe
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retorna la puntuación más alta o 0 si no tiene puntuación
        return $result['high_score'] ?? 0;
    }
}

// Manejo de peticiones POST para guardar la puntuación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? null;
    $score = $_POST['score'] ?? null;

    if ($action === 'saveScore' && $userId !== null && $score !== null) {
        try {
            $gameId = 1; // ID de juego "TicTacToe"
            echo json_encode(TicTacToeScoreController::saveScore($userId, $gameId, $score));
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Parámetros insuficientes o inválidos']);
    }
}
?>
