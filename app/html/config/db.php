<?php

class Db {
    private static $pdo = null; // Inicializar como null

    public static function getConnection() {
        if (self::$pdo === null) {
            $host = 'localhost';
            $db = 'gamezone';
            $user = 'root'; // Usuario predeterminado de MySQL en XAMPP
            $pass = ''; // Contraseña predeterminada suele estar vacía en XAMPP

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Error: No se pudo establecer la conexión a la base de datos. Verifica la configuración. " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
