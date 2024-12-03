<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/Score.php';

class SkateScoreController {

    /**
     * Guarda la puntuación en la base de datos.
     *
     * @param int $userId ID del usuario
     * @param int $gameId ID del juego
     * @param int $score Puntuación obtenida
     * @return array Resultado de la operación
     */
    public static function saveScore($userId, $gameId, $score) {
        // Validar los parámetros
        if ($userId === null || $gameId === null || !is_numeric($score)) {
            return ['success' => false, 'error' => 'Datos inválidos para guardar la puntuación'];
        }

        try {
            // Conectar a la base de datos
            $db = Db::getConnection();

            // Obtener las puntuaciones existentes del usuario para este juego
            $query = "SELECT * FROM scores WHERE user_id = :user_id AND game_id = :game_id ORDER BY score DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();
            $existingScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si hay menos de 3 puntuaciones, simplemente inserta la nueva
            if (count($existingScores) < 3) {
                // Insertar la nueva puntuación
                $query = "INSERT INTO scores (user_id, game_id, score, created_at) VALUES (:user_id, :game_id, :score, NOW())";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
                $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Si ya hay 3 puntuaciones, elimina la más baja si la nueva puntuación es mayor
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

            // Obtener las puntuaciones actualizadas del usuario para este juego
            $query = "SELECT * FROM scores WHERE user_id = :user_id AND game_id = :game_id ORDER BY score DESC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->execute();
            $updatedScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Devolver la respuesta
            return [
                'success' => true,
                'message' => 'Puntuación guardada correctamente',
                'updatedScores' => $updatedScores
            ];

        } catch (PDOException $e) {
            // Capturar cualquier error en la base de datos
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        } catch (Exception $e) {
            // Capturar errores generales
            return ['success' => false, 'error' => 'Error inesperado: ' . $e->getMessage()];
        }
    }
}

// Manejo de peticiones POST para guardar la puntuación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_POST['user_id'] ?? null;
    $score = $_POST['score'] ?? null;

    if ($action === 'saveScore' && $userId !== null && $score !== null) {
        try {
            $gameId = 3; // ID de juego "Skate" (3)
            echo json_encode(SkateScoreController::saveScore($userId, $gameId, $score));
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Parámetros insuficientes o inválidos']);
    }
}
?>
