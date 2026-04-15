<?php

/**
 * Database configuration.
 *
 * SQLite (default, portable — works with php -S):
 *   'dsn' => 'sqlite:' . ROOT_DIR . '/database/app.db'
 *
 * MySQL (university, requires WAMP/XAMPP):
 *   'dsn' => 'mysql:host=localhost;dbname=lab5;charset=utf8'
 *   'username' => 'root'
 *   'password' => ''
 */

// Database configuration. By default uses SQLite for portability.
// To use SQL Server set environment variables: DB_DRIVER=sqlsrv, DB_SERVER, DB_NAME, DB_USERNAME, DB_PASSWORD
// Example (Windows Integrated Auth): set DB_DRIVER=sqlsrv & set DB_SERVER=SERVERNAME & set DB_NAME=Encyclopedia

$driver = getenv('DB_DRIVER') ?: 'sqlite';

if ($driver === 'sqlsrv') {
    $server = getenv('DB_SERVER') ?: 'localhost';
    $database = getenv('DB_NAME') ?: 'Encyclopedia';
    $username = getenv('DB_USERNAME') ?: null;
    $password = getenv('DB_PASSWORD') ?: null;

    // Use PDO sqlsrv driver
    $dsn = "sqlsrv:Server={$server};Database={$database}";

    return [
        'dsn' => $dsn,
        'username' => $username,
        'password' => $password,
        'driver' => 'sqlsrv',
    ];
}

// Default: sqlite (portable)
return [
    'dsn' => 'sqlite:' . ROOT_DIR . '/database/app.db',
    'username' => null,
    'password' => null,
    'driver' => 'sqlite',
];
