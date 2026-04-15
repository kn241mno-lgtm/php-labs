<?php
// Helper: apply a large T-SQL script that uses GO separators via PDO (sqlsrv)
// Usage: php tools/apply_sql_server_schema.php path/to/script.sql

if (PHP_SAPI !== 'cli') {
    die('Run from CLI');
}

$script = $argv[1] ?? __DIR__ . '/../database/schema.sql';
if (!file_exists($script)) {
    echo "Script not found: {$script}\n";
    exit(1);
}

require_once __DIR__ . '/../config/init.php';
$config = require ROOT_DIR . '/config/database.php';

if (($config['driver'] ?? '') !== 'sqlsrv') {
    echo "DB driver is not sqlsrv in config/database.php\n";
    exit(1);
}

try {
    $pdo = new PDO($config['dsn'], $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Connection error: " . $e->getMessage() . "\n";
    exit(1);
}

$sql = file_get_contents($script);
// Split by lines that contain only GO (case-insensitive)
$parts = preg_split('/^\s*GO\s*$/mi', $sql);

foreach ($parts as $i => $part) {
    $part = trim($part);
    if ($part === '') continue;
    try {
        echo "Executing batch #" . ($i+1) . "...\n";
        $pdo->exec($part);
    } catch (Exception $e) {
        echo "Error in batch #" . ($i+1) . ": " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Schema applied successfully.\n";
