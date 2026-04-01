<?php

class RegformController extends PageController
{
    public function action_form(): void
    {
        $errors = [];
        $old = [];

        if ($this->request->isPost()) {
            $old = $this->request->allPost();
            $errors = $this->validate($old);

            if (empty($errors)) {
                $_SESSION['reg_success'] = true;
                $_SESSION['reg_data'] = [
                    'login' => is_string($old['login'] ?? '') ? trim($old['login']) : '',
                ];
                $this->redirect('regform/done');
                return;
            }
        }

        $this->render('regform/form', [
            'errors' => $errors,
            'old' => $old,
        ], 'Реєстрація');
    }

    public function action_done(): void
    {
        if (empty($_SESSION['reg_success'])) {
            $this->redirect('regform/form');
            return;
        }

        $data = $_SESSION['reg_data'] ?? [];
        unset($_SESSION['reg_success'], $_SESSION['reg_data']);

        $this->render('regform/done', ['regData' => $data], 'Реєстрація успішна');
    }

    private function validate(array $data): array
    {
        $errors = [];

        $login = is_string($data['login'] ?? '') ? trim($data['login'] ?? '') : '';
        if ($login === '') {
            $errors['login'] = "Логін є обов'язковим.";
        } else {
            if (preg_match('/\s/', $login)) {
                $errors['login'] = 'Логін має бути одним словом без пробілів.';
            } elseif ((function_exists('mb_strlen') ? mb_strlen($login) : strlen($login)) < 5) {
                $errors['login'] = 'Логін має містити щонайменше 5 символів.';
            } elseif (preg_match('/\d/', $login)) {
                $errors['login'] = 'Логін не повинен містити цифри.';
            }
        }

        $password = is_string($data['password'] ?? '') ? ($data['password'] ?? '') : '';
        if ($password === '') {
            $errors['password'] = "Пароль є обов'язковим.";
        } else {
            $pwdLen = function_exists('mb_strlen') ? mb_strlen($password) : strlen($password);
            if ($pwdLen < 5) {
                $errors['password'] = 'Пароль має містити щонайменше 5 символів.';
            } elseif (!preg_match('/\d/', $password)) {
                $errors['password'] = 'Пароль має містити щонайменше одну цифру.';
            }
        }

        $passwordConfirm = is_string($data['password_confirm'] ?? '') ? ($data['password_confirm'] ?? '') : '';
        if ($passwordConfirm === '') {
            $errors['password_confirm'] = "Підтвердження паролю є обов'язковим.";
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Паролі не збігаються.';
        }

        return $errors;
    }
}
