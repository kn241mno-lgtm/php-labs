<?php

require_once __DIR__ . '/Database.php';

class DbHelpers
{
    public static function getAnimeRating(int $animeId): float
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT AVG(score) as avg_score FROM rating WHERE anime_id = :id');
        $stmt->execute([':id' => $animeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['avg_score']) ? (float)$row['avg_score'] : 0.0;
    }

    public static function getAnimeRatingCount(int $animeId): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM rating WHERE anime_id = :id');
        $stmt->execute([':id' => $animeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['cnt']) ? (int)$row['cnt'] : 0;
    }

    public static function getMangaRating(int $mangaId): float
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT AVG(score) as avg_score FROM rating WHERE manga_id = :id');
        $stmt->execute([':id' => $mangaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['avg_score']) ? (float)$row['avg_score'] : 0.0;
    }

    public static function getMangaRatingCount(int $mangaId): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM rating WHERE manga_id = :id');
        $stmt->execute([':id' => $mangaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['cnt']) ? (int)$row['cnt'] : 0;
    }

    public static function getCharacterRating(int $characterId): float
    {
        // Placeholder: no direct rating table for characters in simplified schema
        return 0.0;
    }

    public static function getCharacterRatingCount(int $characterId): int
    {
        return 0;
    }

    public static function getSiteStats(): array
    {
        $db = Database::getInstance();
        $stats = [];
        $rows = $db->query("SELECT (SELECT COUNT(*) FROM anime) AS anime_count,
                                  (SELECT COUNT(*) FROM manga) AS manga_count,
                                  (SELECT COUNT(*) FROM users) AS users_count,
                                  (SELECT COUNT(*) FROM comments WHERE is_deleted = 0) AS comments_count,
                                  (SELECT COUNT(*) FROM news WHERE is_published = 1) AS news_count");
        $row = $rows->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $stats = [
                'anime' => (int)$row['anime_count'],
                'manga' => (int)$row['manga_count'],
                'users' => (int)$row['users_count'],
                'comments' => (int)$row['comments_count'],
                'news' => (int)$row['news_count'],
            ];
        }
        return $stats;
    }

    public static function searchAll(string $query): array
    {
        $db = Database::getInstance();
        $q = '%' . $query . '%';
        $results = [];

        $stmt = $db->prepare('SELECT id as id, title as title, title_ua as title_ua, "anime" as type, cover_url as image_url FROM anime WHERE title LIKE :q OR title_ua LIKE :q');
        $stmt->execute([':q' => $q]);
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

        $stmt = $db->prepare('SELECT id as id, title as title, title_ua as title_ua, "manga" as type, cover_url as image_url FROM manga WHERE title LIKE :q OR title_ua LIKE :q');
        $stmt->execute([':q' => $q]);
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

        $stmt = $db->prepare('SELECT id as id, name as title, name_ua as title_ua, "character" as type, image_url as image_url FROM character WHERE name LIKE :q OR name_ua LIKE :q');
        $stmt->execute([':q' => $q]);
        $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

        return $results;
    }
}
