<?php
// Simple DB setup script for v14
// Run: php scripts/setup_db.php

define('ROOT_DIR', realpath(__DIR__ . '/..'));
$config = require ROOT_DIR . '/config/database.php';

if (strpos($config['dsn'], 'sqlite:') !== 0) {
    echo "Current DB driver is not sqlite.\n";
    echo "If you want to import into SQL Server, set environment variables DB_DRIVER=sqlsrv and run migration from that server.\n";
    exit(1);
}

$dbPath = substr($config['dsn'], strlen('sqlite:'));
$schemaFile = ROOT_DIR . '/database/schema.sql';

if (!file_exists($schemaFile)) {
    echo "schema.sql not found at $schemaFile\n";
    exit(1);
}

$dir = dirname($dbPath);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

try {
    $pdo = new PDO($config['dsn']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to SQLite DB (will create if missing): $dbPath\n";

    $sql = file_get_contents($schemaFile);
    // Remove GO batch separators (from SQL Server import)
    $sql = preg_replace('/\bGO\b;?/mi', ';', $sql);

    // Execute as a single script
    $pdo->exec('BEGIN TRANSACTION;');
    $pdo->exec($sql);
    $pdo->exec('COMMIT;');

    echo "Schema applied successfully.\n";
    echo "You can run the app with: php -S localhost:8000 -t " . ROOT_DIR . "\n";
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
    exit(1);
}
