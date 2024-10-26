<?php
require_once '../../config/db.php';
require_once '../Models/Score.php';

class ScoreController {
    public static function saveScore($userId, $score) {
        $scoreModel = new Score();
        $scoreModel->saveScore($userId, $score);
    }

    public static function getHighScores() {
        $scoreModel = new Score();
        return $scoreModel->getHighScores();
    }
}