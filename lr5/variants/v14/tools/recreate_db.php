<?php
require_once __DIR__ . '/../config/init.php';

$schemaPath = ROOT_DIR . '/database/schema.sql';
$dbPath = ROOT_DIR . '/database/app.db';

if (!file_exists($schemaPath)){
    echo "Schema file not found: {$schemaPath}\n";
    exit(1);
}

// backup existing DB if present
if (file_exists($dbPath)){
    $bak = $dbPath . '.bak.' . date('Ymd_His');
    if (!copy($dbPath, $bak)){
        echo "Failed to backup existing DB to {$bak}\n";
        exit(1);
    }
    echo "Backed up existing DB to: {$bak}\n";
    // remove old DB so we create fresh
    @unlink($dbPath);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    $sql = file_get_contents($schemaPath);
    if ($sql === false) throw new Exception('Could not read schema file');

    // Try to execute whole file first
    try {
        $pdo->exec($sql);
        echo "Executed schema SQL (whole file).\n";
    } catch (Exception $e) {
        echo "Exec whole file failed: " . $e->getMessage() . "\nTrying statement-by-statement...\n";
        $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        $pdo->beginTransaction();
        foreach ($stmts as $s) {
            if ($s === '') continue;
            try {
                $pdo->exec($s);
            } catch (Exception $se) {
                echo "Statement failed: " . substr($s,0,120) . "... -> " . $se->getMessage() . "\n";
            }
        }
        $pdo->commit();
    }

    // Print summary counts
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables (" . count($tables) . "):\n";
    foreach ($tables as $t) echo " - {$t}\n";

    $check = ['anime','manga','users','genre','studio'];
    foreach ($check as $table) {
        $exists = in_array($table, $tables, true);
        if ($exists) {
            $c = $pdo->query("SELECT COUNT(*) as cnt FROM {$table}")->fetch(PDO::FETCH_ASSOC);
            echo "{$table}: " . ($c['cnt'] ?? 0) . "\n";
        } else {
            echo "{$table}: (missing)\n";
        }
    }

    echo "DB recreate finished.\n";
} catch (Throwable $e){
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
