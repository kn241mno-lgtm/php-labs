<?php

class GuestbookController extends PageController
{
    private string $filePath;
    private ?PDO $db = null;

    public function __construct()
    {
        parent::__construct();
        $this->filePath = DATA_DIR . '/comments.jsonl';
        try {
            $this->db = Database::getInstance();
        } catch (Exception $e) {
            $this->db = null;
        }
    }

    public function action_index(): void
    {
        $message = '';
        $errors = [];

        if ($this->request->isPost()) {
            // support posting comments for anime/manga by logged-in users
            $itemType = $this->request->post('item_type', 'guest');
            $itemId = (int)$this->request->post('item_id', 0);
            $comment = trim($this->request->post('comment', ''));

            if ($this->isLoggedIn()) {
                if ($comment === '') {
                    $errors['comment'] = 'Коментар є обов\'язковим.';
                }

                if (empty($errors)) {
                    if ($this->db) {
                        // Map polymorphic inputs to dedicated comment columns in DB schema
                        switch ($itemType) {
                            case 'anime':
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, anime_id, content) VALUES (:user_id, :anime_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':anime_id' => $itemId, ':content' => $comment]);
                                break;
                            case 'manga':
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, manga_id, content) VALUES (:user_id, :manga_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':manga_id' => $itemId, ':content' => $comment]);
                                break;
                            case 'news':
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, news_id, content) VALUES (:user_id, :news_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':news_id' => $itemId, ':content' => $comment]);
                                break;
                            case 'character':
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, character_id, content) VALUES (:user_id, :character_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':character_id' => $itemId, ':content' => $comment]);
                                break;
                            case 'person':
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, person_id, content) VALUES (:user_id, :person_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':person_id' => $itemId, ':content' => $comment]);
                                break;
                            default:
                                // Generic guestbook comment without item linkage
                                $stmt = $this->db->prepare('INSERT INTO comments (user_id, content) VALUES (:user_id, :content)');
                                $stmt->execute([':user_id' => $_SESSION['user_id'], ':content' => $comment]);
                                break;
                        }
                        $message = 'Коментар додано!';
                    } else {
                        // fallback to file-based guestbook (anonymous name field)
                        $name = trim($this->request->post('name', 'Анонім'));
                        $name = str_replace(["\r", "\n"], ' ', $name);
                        $entry = json_encode([
                            'date' => date('Y-m-d H:i'),
                            'name' => $name,
                            'comment' => $comment,
                        ], JSON_UNESCAPED_UNICODE);
                        file_put_contents($this->filePath, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
                        $message = 'Коментар додано!';
                    }
                }
            } else {
                $errors['auth'] = 'Щоб залишити коментар, увійдіть.';
            }
        }

        // After processing POST, redirect back to the referring page (comments live under items).
        if ($this->request->isPost()) {
            if ($message !== '') {
                $_SESSION['flash_success'] = $message;
            }
            if (!empty($errors)) {
                $_SESSION['flash_error'] = implode(' ', $errors);
            }
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header('Location: ' . $referer);
            exit;
        }

        // Prevent direct access to the standalone guestbook page — redirect to home.
        $this->redirect('index/main');
    }

    private function readComments(): array
    {
        // If DB available and comments table exists, load from DB
        if ($this->db) {
            try {
                $stmt = $this->db->prepare('SELECT c.*, u.login, u.avatar_url, u.display_name FROM comments c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.created_at DESC');
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (Exception $e) {
                // fall through to file
            }
        }

        $comments = [];
        if (!file_exists($this->filePath)) {
            return $comments;
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (is_array($entry) && isset($entry['date'], $entry['name'], $entry['comment'])) {
                $comments[] = $entry;
            }
        }

        return array_reverse($comments);
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function action_delete(): void
    {
        // Delete a comment by id (admin only)
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        // check admin role
        $isAdmin = false;
        if ($this->db) {
            $stmt = $this->db->prepare('SELECT role FROM users WHERE id = :id');
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $row = $stmt->fetch();
            $isAdmin = $row && ($row['role'] === 'admin');
        }

        if (!$isAdmin) {
            $this->show404('Немає дозволу.');
            return;
        }

        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('index/main');
            return;
        }

        if ($this->db) {
            $stmt = $this->db->prepare('DELETE FROM comments WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Коментар видалено.';
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }
}
