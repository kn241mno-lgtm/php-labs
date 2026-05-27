<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
$tables = ['anime','manga','news','users','studio','genre'];
foreach ($tables as $t) {
    try {
        $c = $db->query("SELECT COUNT(*) as cnt FROM {$t}")->fetch(PDO::FETCH_ASSOC);
        echo "{$t}: " . ($c['cnt'] ?? 0) . "\n";
        $rows = $db->query("SELECT id, title FROM {$t} LIMIT 5");
        $all = $rows->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all as $r) {
            echo " - " . ($r['id'] ?? '') . " : " . ($r['title'] ?? ($r['login'] ?? '')) . "\n";
        }
    } catch (Throwable $e) {
        echo "Error reading {$t}: " . $e->getMessage() . "\n";
    }
}
