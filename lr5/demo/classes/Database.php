<?php

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require ROOT_DIR . '/config/database.php';

            try {
                self::$instance = new PDO(
                    $config['dsn'],
                    $config['username'],
                    $config['password']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Enable foreign keys for SQLite
                if (strpos($config['dsn'], 'sqlite') === 0) {
                    self::$instance->exec('PRAGMA foreign_keys = ON');
                }
            } catch (PDOException $e) {
                error_log('DB connection error: ' . $e->getMessage());
                die('Помилка підключення до бази даних.');
            }
        }

        return self::$instance;
    }
}
