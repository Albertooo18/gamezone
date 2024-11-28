<?php

class Db {
    private static $pdo = null; // Inicializar como null

    public static function getConnection() {
        if (self::$pdo === null) {
            $host = 'localhost';
            $db = 'gamezone';
            $user = 'root';
            $pass = ''; // Cambia si tienes una contraseña para tu servidor de MySQL.

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Error: No se pudo establecer la conexión a la base de datos. Verifica la configuración.");
            }
        }

        return self::$pdo;
    }
}
