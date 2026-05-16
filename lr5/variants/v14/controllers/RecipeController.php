<?php

class RecipeController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        // List only published news/articles and order by published date
        $stmt = $this->db->prepare('SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT 12');
        $stmt->execute();
        $items = $stmt->fetchAll();

        $this->render('recipe/list', [
            'recipes' => $items,
        ], 'Новини / Статті');
    }

    public function action_create(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $errors = [];
        $old = [];

        if ($this->request->isPost()) {
            $old = $this->request->allPost();
            // basic validation for news/article
            if (trim($old['title'] ?? '') === '') {
                $errors['title'] = 'Заголовок є обов\'язковим.';
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'INSERT INTO news (title, content, summary, category, image_url, author_id, is_published, published_at)
                     VALUES (:title, :content, :summary, :category, :image_url, :author_id, :is_published, :published_at)'
                );
                $stmt->execute([
                    ':title' => trim($old['title']),
                    ':content' => trim($old['content'] ?? ''),
                    ':summary' => trim($old['summary'] ?? ''),
                    ':category' => trim($old['category'] ?? 'Новини'),
                    ':image_url' => trim($old['image_url'] ?? ''),
                    ':author_id' => $_SESSION['user_id'] ?? null,
                    ':is_published' => isset($old['is_published']) ? 1 : 0,
                    ':published_at' => date('Y-m-d H:i:s'),
                ]);

                $_SESSION['flash_success'] = 'Статтю "' . trim($old['title']) . '" додано!';
                $this->redirect('recipe/list');
                return;
            }
        }

        $this->render('recipe/create', [
            'errors' => $errors,
            'old' => $old,
        ], 'Додати статтю');
    }

    public function action_edit(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)$this->request->get('id', 0);

        if ($id <= 0) {
            $this->redirect('recipe/list');
            return;
        }

        // read from news table
        $stmt = $this->db->prepare('SELECT * FROM news WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $recipe = $stmt->fetch();

        if (!$recipe) {
            $this->redirect('recipe/list');
            return;
        }

        $errors = [];

        if ($this->request->isPost()) {
            $data = $this->request->allPost();

            // basic validation
            if (trim($data['title'] ?? '') === '') {
                $errors['title'] = 'Заголовок є обов\'язковим.';
            }

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'UPDATE news SET title = :title, content = :content, summary = :summary, category = :category, image_url = :image_url, is_published = :is_published, published_at = :published_at WHERE id = :id'
                );
                $stmt->execute([
                    ':title' => trim($data['title']),
                    ':content' => trim($data['content'] ?? ''),
                    ':summary' => trim($data['summary'] ?? ''),
                    ':category' => trim($data['category'] ?? 'Новини'),
                    ':image_url' => trim($data['image_url'] ?? ''),
                    ':is_published' => isset($data['is_published']) ? 1 : 0,
                    ':published_at' => date('Y-m-d H:i:s'),
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Статтю оновлено!';
                $this->redirect('recipe/list');
                return;
            }

            $recipe = array_merge($recipe, $data);
        }

        $this->render('recipe/edit', [
            'recipe' => $recipe,
            'errors' => $errors,
        ], 'Редагувати статтю');
    }

    public function action_delete(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        if ($this->request->isPost()) {
            $id = (int)$this->request->post('id', 0);

            if ($id > 0) {
                $stmt = $this->db->prepare('DELETE FROM news WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $_SESSION['flash_success'] = 'Статтю видалено!';
            }
        }

        $this->redirect('recipe/list');
    }

    public function action_view(): void
    {
        $id = (int)$this->request->get('id', 0);
        if ($id <= 0) {
            $this->redirect('recipe/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT n.*, u.display_name AS author FROM news n LEFT JOIN users u ON n.author_id = u.id WHERE n.id = :id');
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();

        if (!$item) {
            $this->show404('Стаття не знайдена');
            return;
        }

        // load comments for this news
        $cstmt = $this->db->prepare('SELECT c.*, u.login, u.avatar_url, u.display_name FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.news_id = :id ORDER BY c.created_at DESC');
        $cstmt->execute([':id' => $id]);
        $comments = $cstmt->fetchAll();

        $this->render('recipe/view', ['item' => $item, 'comments' => $comments], $item['title']);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (trim($data['title'] ?? '') === '') {
            $errors['title'] = 'Заголовок є обов\'язковим.';
        }

        $time = $data['cooking_time'] ?? '';
        if ($time !== '' && (!is_numeric($time) || (int)$time < 0)) {
            $errors['cooking_time'] = 'Час приготування має бути додатнім числом.';
        }

        $servings = $data['servings'] ?? '';
        if ($servings !== '' && (!is_numeric($servings) || (int)$servings < 1)) {
            $errors['servings'] = 'Кількість порцій має бути не менше 1.';
        }

        return $errors;
    }
}
