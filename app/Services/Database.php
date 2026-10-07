<?php
namespace App\Services;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../Config/database.php';
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // Log and fail gracefully
                error_log("Database connection failure: " . $e->getMessage());
                die("A database connection error occurred. Please configure the system correctly.");
            }
        }
        return self::$instance;
    }
}
