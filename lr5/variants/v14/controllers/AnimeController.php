<?php

class AnimeController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        $stmt = $this->db->prepare('SELECT * FROM anime ORDER BY created_at DESC');
        $stmt->execute();
        $items = $stmt->fetchAll();

        $this->render('anime/list', ['anime' => $items], 'Каталог аніме');
    }

    public function action_view(): void
    {
        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('anime/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM anime WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Аніме не знайдено');
            return;
        }

        // load comments
        $cstmt = $this->db->prepare('SELECT c.*, u.login FROM comments c JOIN users u ON u.id = c.user_id WHERE c.item_type = :type AND c.item_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':type' => 'anime', ':id' => $id]);
        $comments = $cstmt->fetchAll();

        $this->render('anime/view', ['item' => $item, 'comments' => $comments], $item['title']);
    }

    public function action_create(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('auth/login');
            return;
        }

        $errors = [];
        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            if (trim($data['title'] ?? '') === '') {
                $errors['title'] = 'Заголовок обов\'язковий.';
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare('INSERT INTO anime (title, title_ua, year, type, status, episodes, description, cover_url) VALUES (:title, :title_ua, :year, :type, :status, :episodes, :description, :cover_url)');
                $stmt->execute([
                    ':title' => $data['title'],
                    ':title_ua' => $data['title_ua'] ?? '',
                    ':year' => $data['year'] ?: null,
                    ':type' => $data['type'] ?? '',
                    ':status' => $data['status'] ?? '',
                    ':episodes' => $data['episodes'] ?: 0,
                    ':description' => $data['description'] ?? '',
                    ':cover_url' => $data['cover_url'] ?? '',
                ]);

                $_SESSION['flash_success'] = 'Аніме додано.';
                $this->redirect('anime/list');
                return;
            }
        }

        $this->render('anime/create', ['errors' => $errors], 'Додати аніме');
    }

    public function action_edit(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('anime/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM anime WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Аніме не знайдено');
            return;
        }

        $errors = [];
        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            if (trim($data['title'] ?? '') === '') {
                $errors['title'] = 'Заголовок обов\'язковий.';
            }

            if (empty($errors)) {
                $ustmt = $this->db->prepare('UPDATE anime SET title = :title, title_ua = :title_ua, year = :year, type = :type, status = :status, episodes = :episodes, description = :description, cover_url = :cover_url WHERE id = :id');
                $ustmt->execute([
                    ':title' => $data['title'],
                    ':title_ua' => $data['title_ua'] ?? '',
                    ':year' => $data['year'] ?: null,
                    ':type' => $data['type'] ?? '',
                    ':status' => $data['status'] ?? '',
                    ':episodes' => $data['episodes'] ?: 0,
                    ':description' => $data['description'] ?? '',
                    ':cover_url' => $data['cover_url'] ?? '',
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Аніме оновлено.';
                $this->redirect('anime/view&id=' . $id);
                return;
            }
            $item = array_merge($item, $data);
        }

        $this->render('anime/edit', ['item' => $item, 'errors' => $errors], 'Редагувати аніме');
    }

    public function action_delete(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)($this->request->get('id', 0));
        if ($id > 0 && $this->request->isPost()) {
            $stmt = $this->db->prepare('DELETE FROM anime WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Аніме видалено.';
        }

        $this->redirect('anime/list');
    }

    private function isAdmin(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        $stmt = $this->db->prepare('SELECT role FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $row = $stmt->fetch();
        return $row && ($row['role'] === 'admin');
    }
}
