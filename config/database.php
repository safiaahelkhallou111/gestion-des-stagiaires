<?php
// Configuration PDO pour MySQL avec fallback automatique vers SQLite

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

class Database {
    private static $host = '127.0.0.1';
    private static $db_name = 'ocp_stagiaires';
    private static $username = 'root';
    private static $password = '';
    private static $conn = null;
    private static $driver = 'mysql';

    public static function getConnection() {
        if (self::$conn === null) {
            // 1. Essai de connexion à MySQL sur localhost
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$conn = new PDO($dsn, self::$username, self::$password, $options);
                self::$driver = 'mysql';
            } catch (Throwable $e) {
                // 2. Fallback automatique vers SQLite embarqué si MySQL n'est pas actif
                $sqlite_path = ROOT_PATH . '/database/ocp_stagiaires.sqlite';
                try {
                    self::$conn = new PDO("sqlite:" . $sqlite_path, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    self::$driver = 'sqlite';
                } catch (Throwable $e2) {
                    die("Erreur critique de connexion BDD : " . $e2->getMessage());
                }
            }
        }
        return self::$conn;
    }

    public static function getDriver() {
        return self::$driver;
    }
}
