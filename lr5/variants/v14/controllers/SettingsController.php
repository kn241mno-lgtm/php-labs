<?php

class SettingsController extends PageController
{
    private array $availableColors = [
        '#f9fafb' => 'Стандартний (світло-сірий)',
        '#dbeafe' => 'Блакитний',
        '#dcfce7' => 'Зелений',
        '#fef9c3' => 'Жовтий',
        '#fce7f3' => 'Рожевий',
        '#f3e8ff' => 'Фіолетовий',
        '#0b1b2c' => 'Темна тема',
        '#ffffff' => 'Білий',
    ];

    public function action_color(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost()) {
            $color = $this->request->post('bg_color', '#0b1b2c');

            if (array_key_exists($color, $this->availableColors)) {
                $_SESSION['bg_color'] = $color;
                $message = 'Колір фону збережено!';
            } else {
                $error = 'Невідомий колір.';
            }
        }

        $this->render('settings/color', [
            'colors' => $this->availableColors,
            'currentColor' => $_SESSION['bg_color'] ?? '#0b1b2c',
            'message' => $message,
            'error' => $error,
        ], 'Колір фону');
    }

    public function action_greeting(): void
    {
        $message = '';
        $error = '';

        if ($this->request->isPost()) {
            $name = trim($this->request->post('greeting_name', ''));
            $gender = $this->request->post('greeting_gender', '');

            if ($name === '') {
                $error = "Ім'я не може бути порожнім.";
            } elseif (!in_array($gender, ['male', 'female'], true)) {
                $error = 'Оберіть стать.';
            } else {
                setcookie('greeting_name', $name, time() + 30 * 24 * 3600, '/');
                setcookie('greeting_gender', $gender, time() + 30 * 24 * 3600, '/');

                $_COOKIE['greeting_name'] = $name;
                $_COOKIE['greeting_gender'] = $gender;

                $message = 'Привітання збережено в cookie!';
            }
        }

        $this->render('settings/greeting', [
            'message' => $message,
            'error' => $error,
            'currentName' => $_COOKIE['greeting_name'] ?? '',
            'currentGender' => $_COOKIE['greeting_gender'] ?? '',
        ], 'Привітання (Cookie)');
    }

    public function action_profile(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $errors = [];
        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            // reuse simple validation rules
            if (trim($data['first_name'] ?? '') === '') $errors['first_name'] = "Ім'я є обов'язковим.";
            if (trim($data['last_name'] ?? '') === '') $errors['last_name'] = 'Прізвище є обов\'язковим.';
            if (trim($data['email'] ?? '') === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Введіть коректний E-mail.';

            if (empty($errors)) {
                $ustmt = $db->prepare('UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name, phone = :phone, city = :city, gender = :gender, about = :about WHERE id = :id');
                $ustmt->execute([
                    ':email' => trim($data['email']),
                    ':first_name' => trim($data['first_name'] ?? ''),
                    ':last_name' => trim($data['last_name'] ?? ''),
                    ':phone' => trim($data['phone'] ?? ''),
                    ':city' => trim($data['city'] ?? ''),
                    ':gender' => $data['gender'] ?? '',
                    ':about' => trim($data['about'] ?? ''),
                    ':id' => $user['id'],
                ]);

                $_SESSION['flash_success'] = 'Профіль оновлено.';
                $this->redirect('settings/profile');
                return;
            }
            $user = array_merge($user, $data);
        }

        $this->render('settings/profile', ['user' => $user, 'errors' => $errors], 'Налаштування профілю');
    }

    public function action_index(): void
    {
        // simple index with links to subpages
        $this->render('settings/index', [], 'Налаштування');
    }

    public function action_security(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        // placeholder security settings
        $this->render('settings/security', [], 'Безпека');
    }

    public function action_notifications(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        $this->render('settings/notifications', [], 'Сповіщення');
    }

    public function action_customization(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        $this->render('settings/customization', [], 'Кастомізація');
    }
}
