<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';

// Ensure using sqlite driver for init
putenv('DB_DRIVER=sqlite');

$schemaPath = ROOT_DIR . '/database/schema.sql';
$dbPath = ROOT_DIR . '/database/app.db';

try {
    $db = Database::getInstance();

    if (!file_exists($schemaPath)) {
        echo "Schema file not found: {$schemaPath}\n";
        exit(1);
    }

    $sql = file_get_contents($schemaPath);
    // Split into statements and execute only DDL/PRAGMA statements to avoid INSERT errors
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $db->beginTransaction();
    foreach ($statements as $statement) {
        if (preg_match('/^\s*(PRAGMA|CREATE|ALTER|CREATE INDEX)/i', $statement)) {
            $db->exec($statement);
        }
    }
    $db->commit();

    echo "Schema DDL executed (DB path: {$dbPath}) — INSERTs skipped to avoid conflicts.\n";

    // Print a quick summary of tables
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo " - {$t}\n";
    }

    // Print counts for a few tables if exist
    $check = ['anime','manga','users','genre','studio'];
    foreach ($check as $table) {
        $exists = in_array($table, $tables, true);
        if ($exists) {
            $c = $db->query("SELECT COUNT(*) as cnt FROM {$table}")->fetch(PDO::FETCH_ASSOC);
            echo "{$table}: " . ($c['cnt'] ?? 0) . "\n";
        }
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done.\n";
