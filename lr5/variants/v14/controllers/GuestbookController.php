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
                        $stmt = $this->db->prepare('INSERT INTO comments (user_id, item_type, item_id, content) VALUES (:user_id, :item_type, :item_id, :content)');
                        $stmt->execute([
                            ':user_id' => $_SESSION['user_id'],
                            ':item_type' => $itemType,
                            ':item_id' => $itemId,
                            ':content' => $comment,
                        ]);
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

        $comments = $this->readComments();

        $this->render('guestbook/index', [
            'comments' => $comments,
            'message' => $message,
            'errors' => $errors,
        ], 'Гостьова книга');
    }

    private function readComments(): array
    {
        // If DB available and comments table exists, load from DB
        if ($this->db) {
            try {
                $stmt = $this->db->prepare('SELECT c.*, u.login FROM comments c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.created_at DESC');
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
}
