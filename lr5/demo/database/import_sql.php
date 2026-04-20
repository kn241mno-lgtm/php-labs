<?php
// Simple SQLite import helper.
// Usage: run from command line: php import_sql.php

$dbFile = __DIR__ . '/app.db';
$schemaFile = __DIR__ . '/anime_schema.sql';
$importFile = __DIR__ . '/import.sql';

if (!extension_loaded('pdo_sqlite')) {
    echo "PDO SQLite extension is required.\n";
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Using DB: " . $dbFile . "\n";

// Apply local anime schema scaffold
if (file_exists($schemaFile)) {
    $sql = file_get_contents($schemaFile);
    try {
        $pdo->exec($sql);
        echo "Applied anime schema from anime_schema.sql\n";
    } catch (Exception $e) {
        echo "Error applying anime schema: " . $e->getMessage() . "\n";
    }
} else {
    echo "Schema file anime_schema.sql not found, skipping.\n";
}

// If an import.sql exists, attempt to execute its statements.
if (file_exists($importFile)) {
    echo "Found import.sql — executing statements.\n";
    $content = file_get_contents($importFile);
    // Split statements naively by semicolon — works for many simple dumps.
    $statements = array_filter(array_map('trim', explode(';', $content)));
    $executed = 0;
    foreach ($statements as $stmt) {
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
            $executed++;
        } catch (Exception $e) {
            echo "Statement failed: " . substr($stmt, 0, 120) . "... => " . $e->getMessage() . "\n";
        }
    }
    echo "Executed approximately {$executed} statements from import.sql\n";
} else {
    echo "No import.sql found in database folder. Place your dump as import.sql to import.\n";
}

echo "Done. Review output above for errors.\n";

?>
