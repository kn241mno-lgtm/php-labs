<?php
$path = __DIR__ . '/../database/app.db';
if (!file_exists($path)) {
    echo "Database not found: $path\n";
    exit(1);
}
$db = new PDO('sqlite:' . $path);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create tables if missing
$db->exec("CREATE TABLE IF NOT EXISTS author (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    bio TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);");

$db->exec("CREATE TABLE IF NOT EXISTS manga_author (
    manga_id INTEGER,
    author_id INTEGER,
    PRIMARY KEY (manga_id, author_id),
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE
);");

// Seed authors if empty
$count = (int)$db->query('SELECT COUNT(*) FROM author')->fetchColumn();
if ($count > 0) {
    echo "Authors already present: $count\n";
} else {
    $authors = [
        [1, 'Kentaro Miura', 'Автор Berserk'],
        [2, 'Takehiko Inoue', 'Автор Vagabond'],
        [3, 'ONE', 'Автор One Punch Man'],
        [4, 'Tatsuki Fujimoto', 'Автор Chainsaw Man'],
        [5, 'Chugong', 'Автор Solo Leveling'],
        [6, 'Eiichiro Oda', 'Автор One Piece'],
        [7, 'Hajime Isayama', 'Автор Attack on Titan']
    ];

    $stmt = $db->prepare('INSERT INTO author (id, name, bio) VALUES (:id, :name, :bio)');
    foreach ($authors as $a) {
        $stmt->execute([':id'=>$a[0], ':name'=>$a[1], ':bio'=>$a[2]]);
    }
    echo "Inserted " . count($authors) . " authors\n";

    // Seed manga_author mapping for some known IDs
    $mappings = [
        [1,1],[2,2],[3,3],[4,4],[5,5],[6,6],[7,7]
    ];
    $mst = $db->prepare('INSERT OR IGNORE INTO manga_author (manga_id, author_id) VALUES (:m, :a)');
    foreach ($mappings as $m) {
        $mst->execute([':m'=>$m[0], ':a'=>$m[1]]);
    }
    echo "Inserted " . count($mappings) . " manga_author mappings\n";
}

echo "Done.\n";
