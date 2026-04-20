<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
function reportDupes($db, $table) {
    try {
        $sql = "SELECT title, COUNT(*) as c FROM {$table} GROUP BY title HAVING c>1";
        $q = $db->query($sql);
        $rows = $q ? $q->fetchAll(PDO::FETCH_ASSOC) : [];
        if (empty($rows)) {
            echo "No duplicate titles in {$table}\n";
            return;
        }
        echo "Duplicates in {$table}:\n";
        foreach ($rows as $r) {
            echo " - " . ($r['title'] ?? '(null)') . " -> " . ($r['c'] ?? 0) . "\n";
        }
    } catch (Throwable $e) {
        echo "Error checking {$table}: " . $e->getMessage() . "\n";
    }
}

reportDupes($db, 'anime');
reportDupes($db, 'manga');

echo "Done.\n";
