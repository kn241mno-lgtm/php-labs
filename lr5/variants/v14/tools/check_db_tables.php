<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';
try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Tables in database:\n";
    foreach ($rows as $r) echo $r['name'] . "\n";
} catch (Exception $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}
