<?php

class UploadController extends PageController
{
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private int $maxSize = 5 * 1024 * 1024; // 5 MB

    public function __construct()
    {
        parent::__construct();
        $this->uploadDir = DATA_DIR . '/uploads';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function action_index(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost() && isset($_FILES['image'])) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Помилка завантаження файлу (код: ' . $file['error'] . ').';
            } elseif (!in_array($file['type'], $this->allowedTypes, true)) {
                $error = 'Дозволені формати: JPEG, PNG, GIF, WebP.';
            } elseif ($file['size'] > $this->maxSize) {
                $error = 'Максимальний розмір файлу: 5 МБ.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = $this->uploadDir . '/' . $safeName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $message = 'Зображення "' . htmlspecialchars($file['name']) . '" завантажено!';
                } else {
                    $error = 'Не вдалося зберегти файл.';
                }
            }
        }

        $images = $this->getImages();

        $this->render('upload/index', [
            'images' => $images,
            'message' => $message,
            'error' => $error,
        ], 'Завантаження зображень');
    }

    private function getImages(): array
    {
        $images = [];
        $files = glob($this->uploadDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        if ($files) {
            rsort($files);
            foreach ($files as $file) {
                $images[] = [
                    'name' => basename($file),
                    'url' => 'data/uploads/' . basename($file),
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i', filemtime($file)),
                ];
            }
        }

        return $images;
    }
}
