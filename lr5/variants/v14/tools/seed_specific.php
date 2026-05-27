<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
$sql = file_get_contents(ROOT_DIR . '/database/schema.sql');
$lines = preg_split('/\r?\n/', $sql);
$targets = ['INSERT OR IGNORE INTO anime', 'INSERT OR IGNORE INTO manga', 'INSERT OR IGNORE INTO news'];
$current = '';
$collect = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($current === '' && $trim === '') continue;
    if ($current === '' ) {
        foreach ($targets as $t) {
            if (stripos($trim, $t) === 0) {
                $current = $trim;
                break;
            }
        }
    } else {
        $current .= "\n" . $line;
    }
    if ($current !== '' && substr(trim($line), -1) === ';') {
        $collect[] = $current;
        $current = '';
    }
}

if (empty($collect)) {
    echo "No targeted INSERT blocks found.\n";
    exit(0);
}

$db->exec('PRAGMA foreign_keys = OFF');
$db->beginTransaction();
foreach ($collect as $st) {
    try {
        $db->exec($st);
        echo "Executed insert block (len=" . strlen($st) . ")\n";
    } catch (Throwable $e) {
        echo "Insert block error: " . $e->getMessage() . "\n";
    }
}
$db->commit();
$db->exec('PRAGMA foreign_keys = ON');

echo "Done.\n";
