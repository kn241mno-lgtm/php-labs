<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
echo "Tables in DB:\n";
$stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo " - {$t}\n";
}

if (in_array('users', $tables, true)) {
    echo "\nusers table columns:\n";
    $cols = $db->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['cid']}: {$c['name']} ({$c['type']})" . ($c['notnull'] ? ' NOT NULL' : '') . "\n";
    }
}
