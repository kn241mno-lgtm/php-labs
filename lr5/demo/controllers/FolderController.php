<?php

class FolderController extends PageController
{
    private string $usersDir;

    public function __construct()
    {
        parent::__construct();
        $this->usersDir = DATA_DIR . '/users';

        if (!is_dir($this->usersDir)) {
            mkdir($this->usersDir, 0755, true);
        }
    }

    public function action_create(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost()) {
            $login = trim($this->request->post('login', ''));
            $password = trim($this->request->post('password', ''));

            if ($login === '' || $password === '') {
                $error = 'Логін та пароль є обов\'язковими.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $login)) {
                $error = 'Логін: 1-64 символи (латинські літери, цифри, _).';
            } else {
                $userDir = $this->usersDir . '/' . $login;

                if (is_dir($userDir)) {
                    $error = "Папка для користувача \"{$login}\" вже існує!";
                } else {
                    mkdir($userDir, 0755, true);

                    $subfolders = ['video', 'music', 'photo'];
                    foreach ($subfolders as $sub) {
                        $subPath = $userDir . '/' . $sub;
                        mkdir($subPath, 0755, true);

                        // Create sample files
                        file_put_contents($subPath . '/readme.txt', "Папка {$sub} користувача {$login}\nСтворено: " . date('Y-m-d H:i'));
                        file_put_contents($subPath . '/example_1.txt', "Приклад файлу 1 в {$sub}");
                        file_put_contents($subPath . '/example_2.txt', "Приклад файлу 2 в {$sub}");
                    }

                    // Save password hash
                    file_put_contents($userDir . '/.password', password_hash($password, PASSWORD_DEFAULT));

                    $message = "Папку \"{$login}\" створено з підпапками video, music, photo!";
                }
            }
        }

        $folders = $this->getUserFolders();

        $this->render('folder/create', [
            'message' => $message,
            'error' => $error,
            'folders' => $folders,
        ], 'Створення каталогу');
    }

    public function action_delete(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost()) {
            $login = trim($this->request->post('login', ''));
            $password = trim($this->request->post('password', ''));

            if ($login === '' || $password === '') {
                $error = 'Логін та пароль є обов\'язковими.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $login)) {
                $error = 'Логін: 1-64 символи (латинські літери, цифри, _).';
            } else {
                $userDir = $this->usersDir . '/' . $login;

                if (!is_dir($userDir)) {
                    $error = "Папку \"{$login}\" не знайдено.";
                } else {
                    $hashFile = $userDir . '/.password';
                    if (!file_exists($hashFile)) {
                        $error = 'Файл пароля не знайдено. Видалення неможливе.';
                    } elseif (!password_verify($password, file_get_contents($hashFile))) {
                        $error = 'Невірний пароль.';
                    } else {
                        $this->deleteDirectory($userDir);
                        $message = "Папку \"{$login}\" з усім вмістом видалено!";
                    }
                }
            }
        }

        $this->render('folder/delete', [
            'message' => $message,
            'error' => $error,
        ], 'Видалення каталогу');
    }

    private function getUserFolders(): array
    {
        $folders = [];
        $dirs = glob($this->usersDir . '/*', GLOB_ONLYDIR);

        if ($dirs) {
            foreach ($dirs as $dir) {
                $name = basename($dir);
                $subfolders = [];
                $subDirs = glob($dir . '/*', GLOB_ONLYDIR);
                if ($subDirs) {
                    foreach ($subDirs as $subDir) {
                        $subName = basename($subDir);
                        $fileCount = count(glob($subDir . '/*'));
                        $subfolders[] = ['name' => $subName, 'files' => $fileCount];
                    }
                }
                $folders[] = ['name' => $name, 'subfolders' => $subfolders];
            }
        }

        return $folders;
    }

    private function deleteDirectory(string $dir): void
    {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($dir);
    }
}
