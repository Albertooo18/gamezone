<?php

class Db {
    private static $pdo = null; // Inicializar como null

    public static function getConnection() {
        if (self::$pdo === null) {
            $host = 'db'; // Nombre del servicio en docker-compose.yml
            $db = 'gamezone';
            $user = 'user'; // Usuario que configuraste en docker-compose.yml
            $pass = 'password'; // Contraseña que configuraste en docker-compose.yml

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Error: No se pudo establecer la conexión a la base de datos. Verifica la configuración. " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
