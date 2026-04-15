<?php

class MangaController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        $stmt = $this->db->prepare('SELECT * FROM manga ORDER BY created_at DESC');
        $stmt->execute();
        $items = $stmt->fetchAll();

        $this->render('manga/list', ['manga' => $items], 'Каталог манги');
    }

    public function action_view(): void
    {
        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('manga/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM manga WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Манга не знайдена');
            return;
        }

        $cstmt = $this->db->prepare('SELECT c.*, u.login FROM comments c JOIN users u ON u.id = c.user_id WHERE c.item_type = :type AND c.item_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':type' => 'manga', ':id' => $id]);
        $comments = $cstmt->fetchAll();

        $this->render('manga/view', ['item' => $item, 'comments' => $comments], $item['title']);
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
                $stmt = $this->db->prepare('INSERT INTO manga (title, title_ua, year, type, status, chapters, description, cover_url) VALUES (:title, :title_ua, :year, :type, :status, :chapters, :description, :cover_url)');
                $stmt->execute([
                    ':title' => $data['title'],
                    ':title_ua' => $data['title_ua'] ?? '',
                    ':year' => $data['year'] ?: null,
                    ':type' => $data['type'] ?? '',
                    ':status' => $data['status'] ?? '',
                    ':chapters' => $data['chapters'] ?: 0,
                    ':description' => $data['description'] ?? '',
                    ':cover_url' => $data['cover_url'] ?? '',
                ]);

                $_SESSION['flash_success'] = 'Манга додана.';
                $this->redirect('manga/list');
                return;
            }
        }

        $this->render('manga/create', ['errors' => $errors], 'Додати мангу');
    }

    public function action_edit(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)($this->request->get('id', 0));
        if ($id <= 0) {
            $this->redirect('manga/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM manga WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) {
            $this->show404('Манга не знайдена');
            return;
        }

        $errors = [];
        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            if (trim($data['title'] ?? '') === '') {
                $errors['title'] = 'Заголовок обов\'язковий.';
            }

            if (empty($errors)) {
                $ustmt = $this->db->prepare('UPDATE manga SET title = :title, title_ua = :title_ua, year = :year, type = :type, status = :status, chapters = :chapters, description = :description, cover_url = :cover_url WHERE id = :id');
                $ustmt->execute([
                    ':title' => $data['title'],
                    ':title_ua' => $data['title_ua'] ?? '',
                    ':year' => $data['year'] ?: null,
                    ':type' => $data['type'] ?? '',
                    ':status' => $data['status'] ?? '',
                    ':chapters' => $data['chapters'] ?: 0,
                    ':description' => $data['description'] ?? '',
                    ':cover_url' => $data['cover_url'] ?? '',
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Манга оновлена.';
                $this->redirect('manga/view&id=' . $id);
                return;
            }
            $item = array_merge($item, $data);
        }

        $this->render('manga/edit', ['item' => $item, 'errors' => $errors], 'Редагувати мангу');
    }

    public function action_delete(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)($this->request->get('id', 0));
        if ($id > 0 && $this->request->isPost()) {
            $stmt = $this->db->prepare('DELETE FROM manga WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Манга видалена.';
        }

        $this->redirect('manga/list');
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
