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
        // Filtering params
        $q = trim($this->request->get('q', ''));
        $studioId = (int)($this->request->get('studioId', 0));
        $studiosParam = trim($this->request->get('studios', ''));
        $type = trim($this->request->get('type', ''));
        $status = trim($this->request->get('status', ''));
        $yearFrom = (int)($this->request->get('yearFrom', 0));
        $yearTo = (int)($this->request->get('yearTo', 0));
        $genre = trim($this->request->get('genres', ''));// comma-separated or single id
        if ($genre === '') {
            // support older view that used 'genre' param
            $genre = trim($this->request->get('genre', ''));
        }
        $minRating = (float)($this->request->get('minRating', 0));
        $sort = trim($this->request->get('sort', 'rating'));
        $page = max(1, (int)($this->request->get('page', 1)));
        $pageSize = max(6, min(48, (int)($this->request->get('pageSize', 24))));

        $params = [];
        $where = ['1=1'];
        $joins = [];

        if ($q !== '') {
            $where[] = '(a.title LIKE :q OR a.title_ua LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }

        // support multiple studios via comma-separated 'studios' param, fall back to single studioId
        $studioIds = [];
        if ($studiosParam !== '') {
            $parts = array_filter(array_map('trim', explode(',', $studiosParam)));
            foreach ($parts as $p) {
                $id = (int)$p;
                if ($id > 0) $studioIds[] = $id;
            }
        }
        if (!empty($studioIds)) {
            $placeholders = [];
            foreach ($studioIds as $i => $s) {
                $ph = ':s' . $i;
                $placeholders[] = $ph;
                $params[$ph] = $s;
            }
            $where[] = 'a.studio_id IN (' . implode(',', $placeholders) . ')';
        } elseif ($studioId > 0) {
            $where[] = 'a.studio_id = :studioId';
            $params[':studioId'] = $studioId;
        }

        if ($type !== '') {
            $where[] = 'a.type = :type';
            $params[':type'] = $type;
        }

        if ($status !== '') {
            $where[] = 'a.status = :status';
            $params[':status'] = $status;
        }

        if ($yearFrom > 0) {
            $where[] = 'a.year >= :yearFrom';
            $params[':yearFrom'] = $yearFrom;
        }

        if ($yearTo > 0) {
            $where[] = 'a.year <= :yearTo';
            $params[':yearTo'] = $yearTo;
        }

        $genreIds = [];
        if ($genre !== '') {
            // allow either single id or comma-separated list
            $parts = array_filter(array_map('trim', explode(',', $genre)));
            foreach ($parts as $p) {
                $id = (int)$p;
                if ($id > 0) $genreIds[] = $id;
            }
        }

        if (!empty($genreIds)) {
            $joins[] = 'LEFT JOIN anime_genre ag ON ag.anime_id = a.id';
            // create named placeholders :g0,:g1...
            $placeholders = [];
            foreach ($genreIds as $i => $g) {
                $ph = ':g' . $i;
                $placeholders[] = $ph;
                $params[$ph] = $g;
            }
            $where[] = 'ag.genre_id IN (' . implode(',', $placeholders) . ')';
        }

        // build aggregate query: avg rating from rating table
        $order = 'rating DESC, a.created_at DESC';
        switch ($sort) {
            case 'year':
                $order = 'a.year DESC';
                break;
            case 'title':
                $order = 'a.title ASC';
                break;
            case 'views':
                $order = 'a.views DESC';
                break;
        }

        // Count total using grouped query when minRating or genres are used
        $baseSql = 'FROM anime a LEFT JOIN studio s ON a.studio_id = s.id LEFT JOIN rating r2 ON r2.anime_id = a.id ' . (count($joins) ? ' ' . implode(' ', $joins) : '') . ' WHERE ' . implode(' AND ', $where);

        // If minRating filter present, we'll use HAVING on AVG(r2.score)
        $having = '';
        if ($minRating > 0) {
            $having = ' HAVING AVG(r2.score) >= :minRating';
        }

        // total count of groups
        $countSql = 'SELECT COUNT(DISTINCT a.id) ' . $baseSql . $having;
        $countStmt = $this->db->prepare($countSql);
        $countParams = $params;
        if ($minRating > 0) {
            $countParams[':minRating'] = $minRating;
        }
        $countStmt->execute($countParams);
        $total = (int)$countStmt->fetchColumn();

        $totalPages = (int)ceil($total / $pageSize);
        $offset = ($page - 1) * $pageSize;

        // final select with aggregation
        $selectSql = 'SELECT a.*, s.name AS studio_name, IFNULL(AVG(r2.score),0) AS rating ' . $baseSql . ' GROUP BY a.id' . $having . ' ORDER BY ' . $order . ' LIMIT :limit OFFSET :offset';

        $selectStmt = $this->db->prepare($selectSql);
        $selectParams = $params;
        if ($minRating > 0) $selectParams[':minRating'] = $minRating;
        $selectParams[':limit'] = $pageSize;
        $selectParams[':offset'] = $offset;
        $selectStmt->execute($selectParams);
        $items = $selectStmt->fetchAll();

        // fetch reference data for filters
        // fetch genre color too so UI can style chips
        $genres = $this->db->query('SELECT id, name, color FROM genre ORDER BY name')->fetchAll();
        $studios = $this->db->query('SELECT id, name FROM studio ORDER BY name')->fetchAll();

        $this->render('anime/list', [
            'anime' => $items,
            'pagination' => ['page' => $page, 'pageSize' => $pageSize, 'total' => $total, 'totalPages' => $totalPages],
            'filters' => ['q'=>$q,'studioId'=>$studioId,'studios'=>$studiosParam,'type'=>$type,'status'=>$status,'yearFrom'=>$yearFrom,'yearTo'=>$yearTo,'genre'=>$genre,'sort'=>$sort],
            'genres' => $genres,
            'studios' => $studios
        ], 'Каталог аніме');
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
        $cstmt = $this->db->prepare('SELECT c.*, u.login FROM comments c JOIN users u ON u.id = c.user_id WHERE c.anime_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':id' => $id]);
        $comments = $cstmt->fetchAll();

        // load genres for this anime
        $gstmt = $this->db->prepare('SELECT g.id, g.name FROM anime_genre ag JOIN genre g ON ag.genre_id = g.id WHERE ag.anime_id = :id');
        $gstmt->execute([':id' => $id]);
        $genres = $gstmt->fetchAll();

        // load characters linked to this anime
        $c2 = $this->db->prepare('SELECT ch.id, ch.name_ua, ch.name, ch.image_url FROM anime_character ac JOIN character ch ON ac.character_id = ch.id WHERE ac.anime_id = :id');
        $c2->execute([':id' => $id]);
        $characters = $c2->fetchAll();

        // if there are characters, find manga that share them
        $relatedManga = [];
        if (!empty($characters)) {
            $ids = array_map(function($r){ return (int)$r['id']; }, $characters);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $mstmt = $this->db->prepare("SELECT DISTINCT m.* FROM manga m JOIN manga_character mc ON mc.manga_id = m.id WHERE mc.character_id IN ($placeholders) LIMIT 8");
            $mstmt->execute($ids);
            $relatedManga = $mstmt->fetchAll();
        }

        $this->render('anime/view', ['item' => $item, 'comments' => $comments, 'genres' => $genres, 'characters' => $characters, 'relatedManga' => $relatedManga], $item['title']);
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
