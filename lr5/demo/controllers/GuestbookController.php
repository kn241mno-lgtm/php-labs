<?php

class GuestbookController extends PageController
{
    private string $filePath;

    public function __construct()
    {
        parent::__construct();
        $this->filePath = DATA_DIR . '/comments.txt';
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
                $date = date('Y-m-d H:i');
                $line = $date . '|' . str_replace('|', ' ', $name) . '|' . str_replace('|', ' ', $comment) . PHP_EOL;
                file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX);
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
            $parts = explode('|', $line, 3);
            if (count($parts) === 3) {
                $comments[] = [
                    'date' => $parts[0],
                    'name' => $parts[1],
                    'comment' => $parts[2],
                ];
            }
        }

        return array_reverse($comments);
    }
}
