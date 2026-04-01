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
        $stmt = $this->db->query('SELECT * FROM recipes ORDER BY id DESC');
        $recipes = $stmt->fetchAll();

        $this->render('recipe/list', [
            'recipes' => $recipes,
        ], 'Рецепти');
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
            $errors = $this->validate($old);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'INSERT INTO recipes (title, category, cooking_time, servings, ingredients, instructions)
                     VALUES (:title, :category, :cooking_time, :servings, :ingredients, :instructions)'
                );
                $stmt->execute([
                    ':title' => trim($old['title']),
                    ':category' => trim($old['category'] ?? ''),
                    ':cooking_time' => (int)($old['cooking_time'] ?? 0),
                    ':servings' => (int)($old['servings'] ?? 1),
                    ':ingredients' => trim($old['ingredients'] ?? ''),
                    ':instructions' => trim($old['instructions'] ?? ''),
                ]);

                $_SESSION['flash_success'] = 'Рецепт "' . trim($old['title']) . '" додано!';
                $this->redirect('recipe/list');
                return;
            }
        }

        $this->render('recipe/create', [
            'errors' => $errors,
            'old' => $old,
        ], 'Додати рецепт');
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

        $stmt = $this->db->prepare('SELECT * FROM recipes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $recipe = $stmt->fetch();

        if (!$recipe) {
            $this->redirect('recipe/list');
            return;
        }

        $errors = [];

        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            $errors = $this->validate($data);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'UPDATE recipes SET title = :title, category = :category, cooking_time = :cooking_time,
                     servings = :servings, ingredients = :ingredients, instructions = :instructions WHERE id = :id'
                );
                $stmt->execute([
                    ':title' => trim($data['title']),
                    ':category' => trim($data['category'] ?? ''),
                    ':cooking_time' => (int)($data['cooking_time'] ?? 0),
                    ':servings' => (int)($data['servings'] ?? 1),
                    ':ingredients' => trim($data['ingredients'] ?? ''),
                    ':instructions' => trim($data['instructions'] ?? ''),
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Рецепт оновлено!';
                $this->redirect('recipe/list');
                return;
            }

            $recipe = array_merge($recipe, $data);
        }

        $this->render('recipe/edit', [
            'recipe' => $recipe,
            'errors' => $errors,
        ], 'Редагувати рецепт');
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
                $stmt = $this->db->prepare('DELETE FROM recipes WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $_SESSION['flash_success'] = 'Рецепт видалено!';
            }
        }

        $this->redirect('recipe/list');
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (trim($data['title'] ?? '') === '') {
            $errors['title'] = 'Назва рецепту є обов\'язковою.';
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
