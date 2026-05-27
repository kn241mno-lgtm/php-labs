<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';

// Ensure using sqlite driver for seeding
putenv('DB_DRIVER=sqlite');

$schemaPath = ROOT_DIR . '/database/schema.sql';

try {
    $db = Database::getInstance();

    if (!file_exists($schemaPath)) {
        echo "Schema file not found: {$schemaPath}\n";
        exit(1);
    }

    $sql = file_get_contents($schemaPath);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    // Disable foreign keys during bulk insert to avoid ordering issues
    $db->exec('PRAGMA foreign_keys = OFF');
    $db->beginTransaction();
    foreach ($statements as $statement) {
        if (preg_match('/^\s*(INSERT|REPLACE)\b/i', $statement)) {
            try {
                $db->exec($statement);
            } catch (Throwable $e) {
                // Log and continue on single-statement errors
                echo "Warning (insert): " . $e->getMessage() . "\n";
            }
        }
    }
    $db->commit();
    $db->exec('PRAGMA foreign_keys = ON');

    echo "Seed INSERTs executed.\n";

    // Print counts for several tables
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach (['anime','manga','users','genre','studio','news'] as $t) {
        if (in_array($t, $tables, true)) {
            $c = $db->query("SELECT COUNT(*) as cnt FROM {$t}")->fetch(PDO::FETCH_ASSOC);
            echo "{$t}: " . ($c['cnt'] ?? 0) . "\n";
        }
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done.\n";
