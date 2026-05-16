<?php

class AuthController extends PageController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function action_register(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('auth/profile');
            return;
        }

        $errors = [];
        $old = [];

        if ($this->request->isPost()) {
            $old = $this->request->allPost();
            $errors = $this->validateRegister($old);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'INSERT INTO users (login, password, email, first_name, last_name, phone, city, gender, about)
                     VALUES (:login, :password, :email, :first_name, :last_name, :phone, :city, :gender, :about)'
                );
                $stmt->execute([
                    ':login' => trim($old['login']),
                    ':password' => password_hash($old['password'], PASSWORD_DEFAULT),
                    ':email' => trim($old['email']),
                    ':first_name' => trim($old['first_name']),
                    ':last_name' => trim($old['last_name']),
                    ':phone' => trim($old['phone'] ?? ''),
                    ':city' => trim($old['city'] ?? ''),
                    ':gender' => $old['gender'] ?? '',
                    ':about' => trim($old['about'] ?? ''),
                ]);

                session_regenerate_id(true);
                $_SESSION['user_id'] = $this->db->lastInsertId();
                $_SESSION['user_login'] = trim($old['login']);
                // set default UI color for new user (can be changed in profile)
                $_SESSION['bg_color'] = '#0b1b2c';
                $this->redirect('auth/profile');
                return;
            }
        }

        $this->render('auth/register', [
            'errors' => $errors,
            'old' => $old,
        ], 'Реєстрація');
    }

    public function action_login(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('auth/profile');
            return;
        }

        $error = '';

        if ($this->request->isPost()) {
            $login = trim($this->request->post('login', ''));
            $password = $this->request->post('password', '');

            if ($login === '' || $password === '') {
                $error = 'Введіть логін та пароль.';
            } else {
                $stmt = $this->db->prepare('SELECT * FROM users WHERE login = :login');
                $stmt->execute([':login' => $login]);
                $user = $stmt->fetch();

                    if ($user) {
                        $stored = $user['password'] ?? '';
                        $ok = false;
                        // if stored is a modern hash, use password_verify
                        if ($stored !== '' && (strpos($stored, '$') === 0 ? password_verify($password, $stored) : false)) {
                            $ok = true;
                        }
                        // fallback: allow plain-text seed passwords (dev only) and upgrade to hashed
                        if (!$ok && $stored !== '' && hash_equals($stored, $password)) {
                            $ok = true;
                            // upgrade to secure hash
                            try {
                                $newHash = password_hash($password, PASSWORD_DEFAULT);
                                $ust = $this->db->prepare('UPDATE users SET password = :pw WHERE id = :id');
                                $ust->execute([':pw' => $newHash, ':id' => $user['id']]);
                            } catch (Exception $e) {
                                // ignore upgrade failure
                            }
                        }

                        if ($ok) {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_login'] = $user['login'];
                                // apply user's saved UI color (if any)
                                if (!empty($user['ui_color'])) {
                                    $_SESSION['bg_color'] = $user['ui_color'];
                                }
                            $this->redirect('auth/profile');
                            return;
                        }
                    }

                    $error = 'Невірний логін або пароль.';
            }
        }

        $this->render('auth/login', [
            'error' => $error,
        ], 'Вхід');
    }

    public function action_profile(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->action_logout();
            return;
        }

        // Handle profile settings update (avatar, display name, ui color)
        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            $fields = [];
            $params = [':id' => $user['id']];

            if (isset($data['display_name'])) {
                $fields[] = 'display_name = :display_name';
                $params[':display_name'] = trim($data['display_name']);
            }
            if (isset($data['avatar_url'])) {
                $fields[] = 'avatar_url = :avatar_url';
                $params[':avatar_url'] = trim($data['avatar_url']);
            }
            if (isset($data['ui_color'])) {
                $color = trim($data['ui_color']);
                if (preg_match('/^#?[0-9a-fA-F]{6}$/', $color)) {
                    if ($color[0] !== '#') $color = '#' . $color;
                    $fields[] = 'ui_color = :ui_color';
                    $params[':ui_color'] = $color;
                }
            }

            if (!empty($fields)) {
                try {
                    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
                    $ust = $this->db->prepare($sql);
                    $ust->execute($params);
                    // refresh user data
                    $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
                    $stmt->execute([':id' => $user['id']]);
                    $user = $stmt->fetch();
                    // apply ui color to session so header updates
                    if (!empty($user['ui_color'])) {
                        $_SESSION['bg_color'] = $user['ui_color'];
                    }
                    $_SESSION['flash_success'] = 'Налаштування збережено.';
                    $this->redirect('auth/profile');
                    return;
                } catch (Exception $e) {
                    // ignore update error but show form again
                    error_log('Profile update failed: ' . $e->getMessage());
                }
            }
        } else {
            // ensure session background color matches stored preference
            if (!empty($user['ui_color'])) {
                $_SESSION['bg_color'] = $user['ui_color'];
            }
        }

        $this->render('auth/profile', [
            'user' => $user,
        ], 'Профіль');
    }

    public function action_edit(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->action_logout();
            return;
        }

        $errors = [];
        $message = '';

        if ($this->request->isPost()) {
            $data = $this->request->allPost();
            $errors = $this->validateEdit($data, $user);

            if (empty($errors)) {
                $stmt = $this->db->prepare(
                    'UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name,
                     phone = :phone, city = :city, gender = :gender, about = :about WHERE id = :id'
                );
                $stmt->execute([
                    ':email' => trim($data['email']),
                    ':first_name' => trim($data['first_name']),
                    ':last_name' => trim($data['last_name']),
                    ':phone' => trim($data['phone'] ?? ''),
                    ':city' => trim($data['city'] ?? ''),
                    ':gender' => $data['gender'] ?? '',
                    ':about' => trim($data['about'] ?? ''),
                    ':id' => $user['id'],
                ]);

                $_SESSION['flash_success'] = 'Профіль оновлено!';
                $this->redirect('auth/profile');
                return;
            }

            $user = array_merge($user, $data);
        }

        $this->render('auth/edit', [
            'user' => $user,
            'errors' => $errors,
        ], 'Редагувати профіль');
    }

    public function action_logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_login']);
        session_regenerate_id(true);
        $this->redirect('index/main');
    }

    public function action_delete(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return;
        }

        if ($this->request->isPost()) {
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute([':id' => $_SESSION['user_id']]);

            unset($_SESSION['user_id'], $_SESSION['user_login']);
            session_regenerate_id(true);

            $_SESSION['flash_success'] = 'Ваш акаунт видалено.';
            $this->redirect('index/main');
            return;
        }

        $this->render('auth/delete', [], 'Видалення акаунту');
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    private function validateRegister(array $data): array
    {
        $errors = [];

        $login = trim($data['login'] ?? '');
        if ($login === '') {
            $errors['login'] = 'Логін є обов\'язковим.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $login)) {
            $errors['login'] = 'Логін: 3-30 символів (латинські літери, цифри, _).';
        } else {
            $stmt = $this->db->prepare('SELECT id FROM users WHERE login = :login');
            $stmt->execute([':login' => $login]);
            if ($stmt->fetch()) {
                $errors['login'] = 'Цей логін вже зайнятий.';
            }
        }

        $password = $data['password'] ?? '';
        $len = function_exists('mb_strlen') ? mb_strlen($password) : strlen($password);
        if ($password === '') {
            $errors['password'] = 'Пароль є обов\'язковим.';
        } elseif ($len < 6) {
            $errors['password'] = 'Пароль має бути не менше 6 символів.';
        }

        $passwordConfirm = $data['password_confirm'] ?? '';
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Паролі не збігаються.';
        }

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'E-mail є обов\'язковим.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Невірний формат E-mail.';
        }

        if (trim($data['first_name'] ?? '') === '') {
            $errors['first_name'] = "Ім'я є обов'язковим.";
        }
        if (trim($data['last_name'] ?? '') === '') {
            $errors['last_name'] = 'Прізвище є обов\'язковим.';
        }

        return $errors;
    }

    private function validateEdit(array $data, array $currentUser): array
    {
        $errors = [];

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'E-mail є обов\'язковим.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Невірний формат E-mail.';
        }

        if (trim($data['first_name'] ?? '') === '') {
            $errors['first_name'] = "Ім'я є обов'язковим.";
        }
        if (trim($data['last_name'] ?? '') === '') {
            $errors['last_name'] = 'Прізвище є обов\'язковим.';
        }

        return $errors;
    }
}
