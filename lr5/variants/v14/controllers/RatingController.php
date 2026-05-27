<?php

class RatingController
{
    public function action_toggle_favorite(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?route=auth/login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $db = Database::getInstance();

        $animeId = isset($_POST['anime_id']) ? (int)$_POST['anime_id'] : null;
        $mangaId = isset($_POST['manga_id']) ? (int)$_POST['manga_id'] : null;

        if (!$animeId && !$mangaId) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        try {
            if ($animeId) {
                $stmt = $db->prepare('SELECT id, is_favorite FROM rating WHERE anime_id = :aid AND user_id = :uid');
                $stmt->execute([':aid' => $animeId, ':uid' => $userId]);
                $row = $stmt->fetch();

                if ($row) {
                    $new = $row['is_favorite'] ? 0 : 1;
                    $u = $db->prepare('UPDATE rating SET is_favorite = :f WHERE id = :id');
                    $u->execute([':f' => $new, ':id' => $row['id']]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (anime_id, user_id, is_favorite, created_at) VALUES (:aid, :uid, 1, CURRENT_TIMESTAMP)');
                    $i->execute([':aid' => $animeId, ':uid' => $userId]);
                }

                // update cached favorites count on anime
                $c = $db->prepare('SELECT COUNT(1) as cnt FROM rating WHERE anime_id = :aid AND is_favorite = 1');
                $c->execute([':aid' => $animeId]);
                $cnt = (int)$c->fetchColumn();
                $db->prepare('UPDATE anime SET favorites = :cnt WHERE id = :id')->execute([':cnt' => $cnt, ':id' => $animeId]);
            }

            if ($mangaId) {
                $stmt = $db->prepare('SELECT id, is_favorite FROM rating WHERE manga_id = :mid AND user_id = :uid');
                $stmt->execute([':mid' => $mangaId, ':uid' => $userId]);
                $row = $stmt->fetch();

                if ($row) {
                    $new = $row['is_favorite'] ? 0 : 1;
                    $u = $db->prepare('UPDATE rating SET is_favorite = :f WHERE id = :id');
                    $u->execute([':f' => $new, ':id' => $row['id']]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (manga_id, user_id, is_favorite, created_at) VALUES (:mid, :uid, 1, CURRENT_TIMESTAMP)');
                    $i->execute([':mid' => $mangaId, ':uid' => $userId]);
                }

                $c = $db->prepare('SELECT COUNT(1) as cnt FROM rating WHERE manga_id = :mid AND is_favorite = 1');
                $c->execute([':mid' => $mangaId]);
                $cnt = (int)$c->fetchColumn();
                $db->prepare('UPDATE manga SET favorites = :cnt WHERE id = :id')->execute([':cnt' => $cnt, ':id' => $mangaId]);
            }
        } catch (Exception $e) {
            // swallow DB errors for now and redirect back
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }

    public function action_set_status(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?route=auth/login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $db = Database::getInstance();

        $animeId = isset($_POST['anime_id']) ? (int)$_POST['anime_id'] : null;
        $mangaId = isset($_POST['manga_id']) ? (int)$_POST['manga_id'] : null;
        $status = isset($_POST['status']) ? trim($_POST['status']) : null;

        if (!$status || (!$animeId && !$mangaId)) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        // Valid statuses
        $validStatuses = ['planning', 'watching', 'watched'];
        if (!in_array($status, $validStatuses)) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        try {
            if ($animeId) {
                $stmt = $db->prepare('SELECT id FROM rating WHERE anime_id = :aid AND user_id = :uid');
                $stmt->execute([':aid' => $animeId, ':uid' => $userId]);
                $row = $stmt->fetch();

                if ($row) {
                    $u = $db->prepare('UPDATE rating SET status = :status WHERE anime_id = :aid AND user_id = :uid');
                    $u->execute([':status' => $status, ':aid' => $animeId, ':uid' => $userId]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (anime_id, user_id, status, created_at) VALUES (:aid, :uid, :status, CURRENT_TIMESTAMP)');
                    $i->execute([':aid' => $animeId, ':uid' => $userId, ':status' => $status]);
                }
            }

            if ($mangaId) {
                $stmt = $db->prepare('SELECT id FROM rating WHERE manga_id = :mid AND user_id = :uid');
                $stmt->execute([':mid' => $mangaId, ':uid' => $userId]);
                $row = $stmt->fetch();

                if ($row) {
                    $u = $db->prepare('UPDATE rating SET status = :status WHERE manga_id = :mid AND user_id = :uid');
                    $u->execute([':status' => $status, ':mid' => $mangaId, ':uid' => $userId]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (manga_id, user_id, status, created_at) VALUES (:mid, :uid, :status, CURRENT_TIMESTAMP)');
                    $i->execute([':mid' => $mangaId, ':uid' => $userId, ':status' => $status]);
                }
            }
        } catch (Exception $e) {
            // swallow DB errors for now and redirect back
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }

    public function action_set_score(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?route=auth/login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $db = Database::getInstance();

        $animeId = isset($_POST['anime_id']) ? (int)$_POST['anime_id'] : null;
        $mangaId = isset($_POST['manga_id']) ? (int)$_POST['manga_id'] : null;
        $score = isset($_POST['score']) ? trim($_POST['score']) : null;

        if (($animeId && !$animeId) && ($mangaId && !$mangaId)) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        if ($score === null || !is_numeric($score)) {
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        $scoreVal = (float)$score;
        if ($scoreVal < 0) $scoreVal = 0;
        if ($scoreVal > 10) $scoreVal = 10;

        try {
            if ($animeId) {
                $stmt = $db->prepare('SELECT id FROM rating WHERE anime_id = :aid AND user_id = :uid');
                $stmt->execute([':aid' => $animeId, ':uid' => $userId]);
                $row = $stmt->fetch();
                if ($row) {
                    $u = $db->prepare('UPDATE rating SET score = :score WHERE id = :id');
                    $u->execute([':score' => $scoreVal, ':id' => $row['id']]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (anime_id, user_id, score, created_at) VALUES (:aid, :uid, :score, CURRENT_TIMESTAMP)');
                    $i->execute([':aid' => $animeId, ':uid' => $userId, ':score' => $scoreVal]);
                }
                // update anime rating by adding 0.01 to the current average when user rates
                $updateStmt = $db->prepare('UPDATE anime SET rating = ROUND(rating + 0.01, 2) WHERE id = :id');
                $updateStmt->execute([':id' => $animeId]);
            }

            if ($mangaId) {
                $stmt = $db->prepare('SELECT id FROM rating WHERE manga_id = :mid AND user_id = :uid');
                $stmt->execute([':mid' => $mangaId, ':uid' => $userId]);
                $row = $stmt->fetch();
                if ($row) {
                    $u = $db->prepare('UPDATE rating SET score = :score WHERE id = :id');
                    $u->execute([':score' => $scoreVal, ':id' => $row['id']]);
                } else {
                    $i = $db->prepare('INSERT INTO rating (manga_id, user_id, score, created_at) VALUES (:mid, :uid, :score, CURRENT_TIMESTAMP)');
                    $i->execute([':mid' => $mangaId, ':uid' => $userId, ':score' => $scoreVal]);
                }
                // update manga rating by adding 0.01 when user rates
                $updateStmt = $db->prepare('UPDATE manga SET rating = ROUND(rating + 0.01, 2) WHERE id = :id');
                $updateStmt->execute([':id' => $mangaId]);
            }
        } catch (Exception $e) {
            // swallow DB errors
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }
}
