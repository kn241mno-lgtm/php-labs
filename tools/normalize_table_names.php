<?php
$path = __DIR__ . '/../lr5/variants/v14/database/schema.sql';
$bak = $path . '.bak.fix.' . date('YmdHis');
if (!file_exists($path)) {
    echo "schema.sql not found\n";
    exit(1);
}
copy($path, $bak);
$contents = file_get_contents($path);
$replacements = [
    '/\banimegenre\b/i' => 'anime_genre',
    '/\banimecharacter\b/i' => 'anime_character',
    '/\bmangagenre\b/i' => 'manga_genre',
    '/\bmangacharacter\b/i' => 'manga_character',
    '/\bauthors?\b/i' => 'author',
];
foreach ($replacements as $pattern => $rep) {
    $contents = preg_replace($pattern, $rep, $contents);
}
file_put_contents($path, $contents);
echo "Normalized table names and backed up to: $bak\n";
