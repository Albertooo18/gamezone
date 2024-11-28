<?php

class Db {
    private static $pdo = null; // Inicializar como null

    public static function getConnection() {
        if (self::$pdo === null) {
            $host = 'db'; // Cambiar localhost por el nombre del servicio en Docker Compose
            $db = 'gamezone';
            $user = 'user'; // Usuario definido en docker-compose.yml
            $pass = 'password'; // Contraseña definida en docker-compose.yml

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
