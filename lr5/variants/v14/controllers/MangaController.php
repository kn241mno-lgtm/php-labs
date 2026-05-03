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
        $genre = trim($this->request->get('genres', ''));
        $yearFrom = (int)($this->request->get('yearFrom', 0));
        $yearTo = (int)($this->request->get('yearTo', 0));
        $ratingFrom = $this->request->get('ratingFrom', '');
        $ratingTo = $this->request->get('ratingTo', '');
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

        if ($yearFrom > 0) {
            $where[] = 'm.year >= :yearFrom';
            $params[':yearFrom'] = $yearFrom;
        }

        if ($yearTo > 0) {
            $where[] = 'm.year <= :yearTo';
            $params[':yearTo'] = $yearTo;
        }

        if ($author > 0) {
            $joins[] = 'JOIN manga_author ma ON ma.manga_id = m.id';
            $where[] = 'ma.author_id = :author';
            $params[':author'] = $author;
        }

        $genreIds = [];
        if ($genre !== '') {
            $parts = array_filter(array_map('trim', explode(',', $genre)));
            foreach ($parts as $p) {
                $id = (int)$p;
                if ($id > 0) $genreIds[] = $id;
            }
        }

        if (!empty($genreIds)) {
            $joins[] = 'LEFT JOIN manga_genre mg ON mg.manga_id = m.id';
            $placeholders = [];
            foreach ($genreIds as $i => $g) {
                $ph = ':g' . $i;
                $placeholders[] = $ph;
                $params[$ph] = $g;
            }
            $where[] = 'mg.genre_id IN (' . implode(',', $placeholders) . ')';
        }

        $order = 'm.title ASC';
        switch ($sort) {
            case 'year': $order = 'm.year DESC'; break;
            case 'views': $order = 'm.views DESC'; break;
        }

        $baseSql = 'FROM manga m LEFT JOIN rating r2 ON r2.manga_id = m.id ' . (count($joins) ? ' ' . implode(' ', $joins) : '') . ' WHERE ' . implode(' AND ', $where);

        // build HAVING clause for rating range (if provided)
        $havingParts = [];
        if ($ratingFrom !== '' && is_numeric($ratingFrom)) {
            $havingParts[] = 'AVG(r2.score) >= :ratingFrom';
        }
        if ($ratingTo !== '' && is_numeric($ratingTo)) {
            $havingParts[] = 'AVG(r2.score) <= :ratingTo';
        }
        $having = '';
        if (!empty($havingParts)) {
            $having = ' HAVING ' . implode(' AND ', $havingParts);
        }

        $countSql = 'SELECT COUNT(DISTINCT m.id) ' . $baseSql . $having;
        $countStmt = $this->db->prepare($countSql);
        $countParams = $params;
        if ($ratingFrom !== '' && is_numeric($ratingFrom)) $countParams[':ratingFrom'] = (float)$ratingFrom;
        if ($ratingTo !== '' && is_numeric($ratingTo)) $countParams[':ratingTo'] = (float)$ratingTo;
        $countStmt->execute($countParams);
        $total = (int)$countStmt->fetchColumn();

        $totalPages = (int)ceil($total / $pageSize);
        $offset = ($page - 1) * $pageSize;

        $selectSql = 'SELECT m.*, IFNULL(AVG(r2.score),0) AS rating ' . $baseSql . ' GROUP BY m.id' . $having . ' ORDER BY ' . $order . ' LIMIT :limit OFFSET :offset';
        $selectStmt = $this->db->prepare($selectSql);
        $selectParams = $params;
        if ($ratingFrom !== '' && is_numeric($ratingFrom)) $selectParams[':ratingFrom'] = (float)$ratingFrom;
        if ($ratingTo !== '' && is_numeric($ratingTo)) $selectParams[':ratingTo'] = (float)$ratingTo;
        $selectParams[':limit'] = $pageSize;
        $selectParams[':offset'] = $offset;
        $selectStmt->execute($selectParams);
        $items = $selectStmt->fetchAll();

        // fetch genres and authors for UI
        $genres = $this->db->query('SELECT id, name FROM genre ORDER BY name')->fetchAll();
        $authors = [];
        try {
            $authors = $this->db->query('SELECT id, name FROM author ORDER BY name')->fetchAll();
        } catch (Exception $e) { $authors = []; }

        $this->render('manga/list', [
            'manga' => $items,
            'pagination' => ['page'=>$page,'pageSize'=>$pageSize,'total'=>$total,'totalPages'=>$totalPages],
            'filters' => [
                'q'=>$q,'status'=>$status,'type'=>$type,'author'=>$author,'genre'=>$genre,
                'yearFrom'=>$this->request->get('yearFrom', ''),'yearTo'=>$this->request->get('yearTo', ''),'sort'=>$sort,
                'ratingFrom'=>$this->request->get('ratingFrom',''),'ratingTo'=>$this->request->get('ratingTo','')
            ],
            'genres'=>$genres,
            'authors'=>$authors
        ], 'Каталог манги');
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

        // load characters for this manga (alias name -> name_ua for compatibility with views)
        $c2 = $this->db->prepare('SELECT ch.id, ch.name AS name_ua, ch.name, ch.image_url FROM manga_character mc JOIN character ch ON mc.character_id = ch.id WHERE mc.manga_id = :id');
        $c2->execute([':id' => $id]);
        $characters = $c2->fetchAll();

        // related anime via same characters
        $relatedAnime = [];
        if (!empty($characters)) {
            $ids = array_map(function($r){ return (int)$r['id']; }, $characters);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $astmt = $this->db->prepare("SELECT DISTINCT a.* FROM anime a JOIN anime_character ac ON ac.anime_id = a.id WHERE ac.character_id IN ($placeholders) LIMIT 8");
            $astmt->execute($ids);
            $relatedAnime = $astmt->fetchAll();
        }

        $this->render('manga/view', ['item' => $item, 'comments' => $comments, 'characters' => $characters, 'relatedAnime' => $relatedAnime], $item['title']);
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
