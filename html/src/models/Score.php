<?php
require_once '../../config/db.php';

class Score {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function saveScore($userId, $score) {
        $stmt = $this->pdo->prepare("INSERT INTO scores (user_id, game, score, played_at) VALUES (?, 'tic_tac_toe', ?, NOW())");
        $stmt->execute([$userId, $score]);
    }

    public function getHighScores() {
        $stmt = $this->pdo->query("SELECT users.username, scores.score, scores.played_at FROM scores JOIN users ON scores.user_id = users.id ORDER BY scores.score DESC LIMIT 10");
        return $stmt->fetchAll();
    }
}