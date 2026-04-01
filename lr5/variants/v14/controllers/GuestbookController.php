<?php

class GuestbookController extends PageController
{
    private string $filePath;

    public function __construct()
    {
        parent::__construct();
        $this->filePath = DATA_DIR . '/comments.jsonl';
    }

    public function action_index(): void
    {
        $message = '';
        $errors = [];

        if ($this->request->isPost()) {
            $name = trim($this->request->post('name', ''));
            $comment = trim($this->request->post('comment', ''));

            if ($name === '') {
                $errors['name'] = "Ім'я є обов'язковим.";
            }
            if ($comment === '') {
                $errors['comment'] = 'Коментар є обов\'язковим.';
            }

            if (empty($errors)) {
                $name = str_replace(["\r", "\n"], ' ', $name);
                $comment = str_replace(["\r", "\n"], ' ', $comment);
                $entry = json_encode([
                    'date' => date('Y-m-d H:i'),
                    'name' => $name,
                    'comment' => $comment,
                ], JSON_UNESCAPED_UNICODE);
                file_put_contents($this->filePath, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
                $message = 'Коментар додано!';
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
}
