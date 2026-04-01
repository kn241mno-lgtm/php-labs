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

return [
    'dsn' => 'sqlite:' . ROOT_DIR . '/database/app.db',
    'username' => null,
    'password' => null,
];
