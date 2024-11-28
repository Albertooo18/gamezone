<?php
require_once __DIR__ . '/../../config/db.php';

class Score {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Db::getConnection(); // Usar la conexión desde Db
        } catch (Exception $e) {
            throw new Exception("Error: No se pudo establecer la conexión a la base de datos. Verifica la configuración.");
        }
    }

    // Guardar puntuación del usuario
    public function saveScore($userId, $gameId, $score) {
        // Validar userId, gameId y score
        if ($userId === null || $gameId === null || !is_numeric($score) || $score < 0) {
            throw new InvalidArgumentException('Datos inválidos para guardar la puntuación');
        }

        // Verificar si ya existe una puntuación para ese usuario y juego
        $stmt = $this->pdo->prepare("SELECT id FROM scores WHERE user_id = ? AND game_id = ?");
        $stmt->execute([$userId, $gameId]);
        $existingScore = $stmt->fetch();

        if ($existingScore) {
            // Actualizar la puntuación existente
            $stmt = $this->pdo->prepare("UPDATE scores SET score = ?, created_at = NOW() WHERE user_id = ? AND game_id = ?");
            $stmt->execute([$score, $userId, $gameId]);
        } else {
            // Insertar una nueva puntuación
            $stmt = $this->pdo->prepare("INSERT INTO scores (user_id, game_id, score, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $gameId, $score]);
        }
    }

    // Obtener las puntuaciones del usuario
    public function getUserScores($userId) {
        if ($userId === null) {
            throw new InvalidArgumentException('ID de usuario inválido');
        }

        $stmt = $this->pdo->prepare("SELECT games.name AS game, scores.score, scores.created_at 
                                     FROM scores 
                                     JOIN games ON scores.game_id = games.id 
                                     WHERE scores.user_id = ? 
                                     ORDER BY scores.created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Obtener las mejores puntuaciones
    public function getHighScores() {
        $stmt = $this->pdo->query("SELECT users.username, games.name AS game, scores.score, scores.created_at 
                                   FROM scores 
                                   JOIN users ON scores.user_id = users.id 
                                   JOIN games ON scores.game_id = games.id 
                                   ORDER BY scores.score DESC 
                                   LIMIT 10");
        return $stmt->fetchAll();
    }

    // Obtener la puntuación máxima del usuario
    public function getUserHighScore($userId) {
        $stmt = $this->pdo->prepare("SELECT MAX(score) as max_score FROM scores WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['max_score'] ?: 0; // Retorna 0 si no hay puntuaciones
    }
}
