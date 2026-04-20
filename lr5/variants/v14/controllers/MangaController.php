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
        $q = trim($this->request->get('q', ''));
        $status = trim($this->request->get('status', ''));
        $type = trim($this->request->get('type', ''));
        $author = (int)($this->request->get('author', 0));
        $genre = (int)($this->request->get('genre', 0));
        $page = max(1, (int)($this->request->get('page', 1)));
        $pageSize = max(6, min(48, (int)($this->request->get('pageSize', 24))));
        $sort = trim($this->request->get('sort', 'title'));

        $params = [];
        $where = ['1=1'];
        $joins = [];

        if ($q !== '') {
            $where[] = '(m.title LIKE :q OR m.title_ua LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }

        if ($status !== '') {
            $where[] = 'm.status = :status';
            $params[':status'] = $status;
        }

        if ($type !== '') {
            $where[] = 'm.type = :type';
            $params[':type'] = $type;
        }

        if ($author > 0) {
            $joins[] = 'JOIN manga_author ma ON ma.manga_id = m.id';
            $where[] = 'ma.author_id = :author';
            $params[':author'] = $author;
        }

        if ($genre > 0) {
            $joins[] = 'JOIN manga_genre mg ON mg.manga_id = m.id';
            $where[] = 'mg.genre_id = :genre';
            $params[':genre'] = $genre;
        }

        $order = 'm.title ASC';
        switch ($sort) {
            case 'year': $order = 'm.year DESC'; break;
            case 'views': $order = 'm.views DESC'; break;
        }

        $sql = 'SELECT m.*, IFNULL(r.avg_rating,0) AS rating FROM manga m LEFT JOIN (SELECT manga_id, AVG(score) AS avg_rating FROM rating GROUP BY manga_id) r ON r.manga_id = m.id ' . (count($joins) ? ' ' . implode(' ', $joins) : '') . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $all = $stmt->fetchAll();

        $total = count($all);
        $totalPages = (int)ceil($total / $pageSize);
        $start = ($page - 1) * $pageSize;
        $items = array_slice($all, $start, $pageSize);

        $this->render('manga/list', ['manga' => $items, 'pagination'=>['page'=>$page,'pageSize'=>$pageSize,'total'=>$total,'totalPages'=>$totalPages], 'filters'=>['q'=>$q,'status'=>$status,'type'=>$type,'author'=>$author,'genre'=>$genre,'sort'=>$sort]], 'Каталог манги');
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

        $cstmt = $this->db->prepare('SELECT c.*, u.login FROM comments c JOIN users u ON u.id = c.user_id WHERE c.manga_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':id' => $id]);
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
