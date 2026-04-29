<?php
require_once __DIR__ . '/../config/init.php';
$schema = ROOT_DIR . '/database/schema.sql';
$bak = $schema . '.bak.' . date('YmdHis');
if (!file_exists($schema)) { echo "schema.sql not found\n"; exit(1); }
$sql = file_get_contents($schema);
$statements = preg_split('/;\s*\n/', $sql);
$map = [
    'TitleUA' => 'title_ua', 'TitleEN' => 'title_en', 'Title' => 'title',
    'NameUA' => 'name_ua', 'NameEN' => 'name_en', 'Name' => 'name',
    'DescriptionUA' => 'description_ua', 'DescriptionEN' => 'description_en', 'Description' => 'description',
    'CoverUrl' => 'cover_url', 'PosterUrl' => 'poster_url', 'AvatarUrl' => 'avatar_url', 'ImageUrl' => 'image_url',
    'Views' => 'views', 'Favorites' => 'favorites', 'CreatedAt' => 'created_at',
    'AnimeID' => 'anime_id', 'MangaID' => 'manga_id', 'CharacterID' => 'character_id', 'GenreID' => 'genre_id', 'UserID' => 'user_id',
    'IsFavorite' => 'is_favorite', 'IsMain' => 'is_main', 'Score' => 'score', 'Progress' => 'progress',
    'DisplayName' => 'display_name', 'BirthDate' => 'birth_date', 'BirthPlace' => 'birth_place',
    'FirstName' => 'first_name', 'LastName' => 'last_name', 'FullName' => 'full_name',
    'Episodes' => 'episodes', 'EpisodeDuration' => 'episode_duration', 'Season' => 'season',
    'Volumes' => 'volumes', 'Chapters' => 'chapters', 'Demographic' => 'demographic',
    'RatingMPAA' => 'rating_mpaa', 'StudioID' => 'studio_id', 'Type' => 'type', 'Status' => 'status', 'Source' => 'source'
];
$out = [];
foreach ($statements as $stmt) {
    $s = trim($stmt);
    if ($s === '') continue;
    if (preg_match('/^\s*INSERT\b/i', $s)) {
        if (preg_match('/^\s*(INSERT\s+(?:OR\s+IGNORE\s+)?INTO\s+\S+)\s*\(([^)]*)\)\s*VALUES\s*(.+)$/is', $s, $m)) {
            $prefix = $m[1];
            $cols = $m[2];
            $rest = $m[3];
            $parts = array_map('trim', explode(',', $cols));
            foreach ($parts as &$p) {
                // remove backticks or brackets
                $clean = preg_replace('/^[`\[\"]|[`\]\"]$/', '', $p);
                foreach ($map as $k => $v) {
                    if (strcasecmp($clean, $k) === 0) { $clean = $v; break; }
                }
                $p = $clean;
            }
            $newcols = implode(', ', $parts);
            $s = $prefix . ' (' . $newcols . ') VALUES ' . $rest;
        } else {
            // leave as-is
        }
    }
    $out[] = $s;
}
// backup and write
file_put_contents($bak, $sql);
file_put_contents($schema, implode(";\n", $out) . ";\n");
echo "Normalized INSERT column names and backed up original to: {$bak}\n";
