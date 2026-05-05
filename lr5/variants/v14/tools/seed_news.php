<?php
$root = realpath(__DIR__ . '/..');
$schema = $root . '/database/schema.sql';
$dbPath = $root . '/database/app.db';
if (!file_exists($dbPath)) { echo "DB not found: $dbPath\n"; exit(1); }
if (!file_exists($schema)) { echo "schema not found: $schema\n"; exit(1); }
$sql = file_get_contents($schema);
if ($sql === false) { echo "Could not read schema\n"; exit(2); }
// find INSERT INTO news ... ; block
if (!preg_match('/INSERT INTO\s+news\s*\(.*?\)\s*VALUES\s*(.*?);/s', $sql, $m)) {
    echo "No INSERT INTO news block found in schema\n";
    exit(3);
}
$valuesPart = trim($m[0]); // full matched INSERT statement
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // check existing count
    $cnt = (int)$db->query('SELECT COUNT(*) as c FROM news')->fetch(PDO::FETCH_ASSOC)['c'];
    if ($cnt > 0) {
        echo "News table already has $cnt rows, skipping seeding.\n";
        exit(0);
    }
    // execute the INSERT statement
    $db->exec($valuesPart);
    $cnt2 = (int)$db->query('SELECT COUNT(*) as c FROM news')->fetch(PDO::FETCH_ASSOC)['c'];
    echo "Inserted news rows, now count: $cnt2\n";
} catch (Exception $e) {
    echo "Error executing seed: " . $e->getMessage() . "\n";
    exit(4);
}
