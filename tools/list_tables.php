<?php
require_once __DIR__ . '/../lr5/variants/v14/config/init.php';
require_once __DIR__ . '/../lr5/variants/v14/classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
$stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables found: \n";
foreach ($tables as $t) echo " - $t\n";
