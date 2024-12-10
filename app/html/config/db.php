<?php

class Db {
    private static $pdo = null; // Inicializar como null

    public static function getConnection() {
        if (self::$pdo === null) {
            // Cambia 'localhost' por la IP privada de la máquina que tiene MariaDB
            $host = '172.31.24.224';  // Cambia esto por la IP privada de la máquina donde está el contenedor MariaDB
            $db = 'gamezone';
            $user = 'user';  // Usuario configurado en docker-compose
            $pass = 'password';  // Contraseña configurada en docker-compose

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // En caso de error en la conexión
                throw new Exception("Error: No se pudo establecer la conexión a la base de datos. Verifica la configuración. " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
