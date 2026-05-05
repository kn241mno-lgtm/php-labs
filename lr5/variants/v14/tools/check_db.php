<?php
$dbPath = __DIR__ . '/../database/app.db';
if (!file_exists($dbPath)) { echo "DB not found: $dbPath\n"; exit(1); }
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rows = $db->query('SELECT id, title, is_published, published_at FROM news ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    echo "news count: " . count($rows) . "\n";
    foreach ($rows as $r) {
        echo json_encode($r) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(2);
}
