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
    // remove existing DB to recreate from schema
    if (file_exists($dbPath)) {
        unlink($dbPath);
        echo "Removed existing DB: $dbPath\n";
    }

    $pdo = new PDO($config['dsn']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to SQLite DB (will create if missing): $dbPath\n";

    $sql = file_get_contents($schemaFile);
    // Remove GO batch separators (from SQL Server import)
    $sql = preg_replace('/^\s*GO\s*$/mi', ';', $sql);

    // Build map of existing table names from CREATE TABLE statements
    $existingTables = [];
    if (preg_match_all('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+([`\"\[]?)([a-zA-Z0-9_]+)\1/i', $sql, $mt)) {
        foreach ($mt[2] as $tname) {
            $key = str_replace('_', '', strtolower($tname));
            $existingTables[$key] = $tname;
        }
    }

    // Normalize column names in INSERT statements (convert CamelCase like NameUA -> name_ua)
    $sql = preg_replace_callback('/INSERT\s+(OR\s+IGNORE\s+)?INTO\s+([\[\]"`\w]+)\s*\(([^)]+)\)/i', function($m) use ($existingTables) {
        $prefix = $m[1] ?? '';
        $table = $m[2];
        // normalize table name (match to existing CREATE TABLE names)
        $rawTable = trim($table, '[]"` ');
        $key = str_replace('_', '', strtolower($rawTable));
        if (isset($existingTables[$key])) {
            $table = $existingTables[$key];
        } else {
            $tbl = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $rawTable);
            $tbl = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $tbl);
            $table = strtolower($tbl);
        }
        $cols = $m[3];
        $colArr = array_map('trim', explode(',', $cols));
        $colArr = array_map(function($c){
            $c = trim($c, '[]"` ');
            $c = str_replace(' ', '_', $c);
            $c = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $c);
            $c = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $c);
            $c = strtolower($c);
            return $c;
        }, $colArr);
        return 'INSERT ' . $prefix . 'INTO ' . $table . ' (' . implode(',', $colArr) . ')';
    }, $sql);

    // Execute statements one by one so we can skip constraint-violating inserts
    $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
    $pdo->exec('BEGIN TRANSACTION;');
    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
        } catch (PDOException $e) {
            // Ignore unique constraint errors and continue
            $msg = $e->getMessage();
            if (stripos($msg, 'unique constraint failed') !== false || stripos($msg, 'UNIQUE constraint failed') !== false || stripos($msg, 'Integrity constraint violation') !== false) {
                // skip
                continue;
            }
            // For other errors, rethrow to help debugging
            throw $e;
        }
    }
    $pdo->exec('COMMIT;');

    echo "Schema applied successfully.\n";
    // Cleanup: remove duplicate manga entries (keep lowest id) and ensure unique index
    try {
        echo "Removing duplicate manga entries (if any)...\n";
        $pdo->exec("DELETE FROM manga WHERE id NOT IN (SELECT MIN(id) FROM manga GROUP BY title, year);");
        // Create unique index to prevent future duplicates by title+year
        $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS ux_manga_title_year ON manga(title, year);");
        echo "Duplicate cleanup complete. Unique index ux_manga_title_year created.\n";
    } catch (PDOException $e) {
        echo "Cleanup warning: " . $e->getMessage() . "\n";
    }
    echo "You can run the app with: php -S localhost:8000 -t " . ROOT_DIR . "\n";
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
    exit(1);
}
