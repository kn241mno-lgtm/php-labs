<?php

class CatalogController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_list(): void
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY id DESC');
        $products = $stmt->fetchAll();

        $this->render('catalog/list', [
            'products' => $products,
        ], 'Каталог товарів');
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
                    'INSERT INTO products (name, price, category, description) VALUES (:name, :price, :category, :description)'
                );
                $stmt->execute([
                    ':name' => trim($old['name']),
                    ':price' => (float)$old['price'],
                    ':category' => trim($old['category'] ?? ''),
                    ':description' => trim($old['description'] ?? ''),
                ]);

                $_SESSION['flash_success'] = 'Товар "' . trim($old['name']) . '" додано!';
                $this->redirect('catalog/list');
                return;
            }
        }

        $this->render('catalog/create', [
            'errors' => $errors,
            'old' => $old,
        ], 'Додати товар');
    }

    public function action_edit(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $id = (int)$this->request->get('id', 0);

        if ($id <= 0) {
            $this->redirect('catalog/list');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            $this->redirect('catalog/list');
            return;
        }

        $errors = [];

        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            $errors = $this->validate($data);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'UPDATE products SET name = :name, price = :price, category = :category, description = :description WHERE id = :id'
                );
                $stmt->execute([
                    ':name' => trim($data['name']),
                    ':price' => (float)$data['price'],
                    ':category' => trim($data['category'] ?? ''),
                    ':description' => trim($data['description'] ?? ''),
                    ':id' => $id,
                ]);

                $_SESSION['flash_success'] = 'Товар оновлено!';
                $this->redirect('catalog/list');
                return;
            }

            $product = array_merge($product, $data);
        }

        $this->render('catalog/edit', [
            'product' => $product,
            'errors' => $errors,
        ], 'Редагувати товар');
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
                $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
                $stmt->execute([':id' => $id]);
                $_SESSION['flash_success'] = 'Товар видалено!';
            }
        }

        $this->redirect('catalog/list');
    }

    private function validate(array $data): array
    {
        $errors = [];

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $errors['name'] = 'Назва є обов\'язковою.';
        }

        $price = $data['price'] ?? '';
        if ($price === '' || $price === null) {
            $errors['price'] = 'Ціна є обов\'язковою.';
        } elseif (!is_numeric($price) || (float)$price < 0) {
            $errors['price'] = 'Ціна має бути додатнім числом.';
        }

        return $errors;
    }
}
