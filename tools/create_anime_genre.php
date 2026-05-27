<?php
require_once __DIR__ . '/../lr5/variants/v14/config/init.php';
require_once __DIR__ . '/../lr5/variants/v14/classes/Database.php';
putenv('DB_DRIVER=sqlite');
$db = Database::getInstance();
$db->exec("CREATE TABLE IF NOT EXISTS anime_genre (
    anime_id INTEGER,
    genre_id INTEGER,
    PRIMARY KEY (anime_id, genre_id),
    FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE CASCADE
)");
echo "anime_genre created or already exists\n";
