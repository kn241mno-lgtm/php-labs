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
                    'first_name' => $old['first_name'],
                    'last_name' => $old['last_name'],
                    'email' => $old['email'],
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

        $firstName = trim($data['first_name'] ?? '');
        if ($firstName === '') {
            $errors['first_name'] = "Ім'я є обов'язковим.";
        } elseif (!preg_match('/^[A-Za-z]+$/u', $firstName)) {
            $errors['first_name'] = "Ім'я має містити лише англійські літери.";
        }

        $lastName = trim($data['last_name'] ?? '');
        if ($lastName === '') {
            $errors['last_name'] = "Прізвище є обов'язковим.";
        } elseif (!preg_match('/^[A-Za-z]+$/u', $lastName)) {
            $errors['last_name'] = 'Прізвище має містити лише англійські літери.';
        }

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $errors['email'] = "E-mail є обов'язковим.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Невірний формат E-mail.';
        }

        $password = $data['password'] ?? '';
        if ($password === '') {
            $errors['password'] = "Пароль є обов'язковим.";
        } elseif (mb_strlen($password) < 6) {
            $errors['password'] = 'Пароль має бути не менше 6 символів.';
        }

        $passwordConfirm = $data['password_confirm'] ?? '';
        if ($passwordConfirm === '') {
            $errors['password_confirm'] = "Підтвердження пароля є обов'язковим.";
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Паролі не збігаються.';
        }

        $gender = $data['gender'] ?? '';
        if ($gender === '') {
            $errors['gender'] = "Стать є обов'язковою.";
        }

        $city = $data['city'] ?? '';
        if ($city === '') {
            $errors['city'] = "Місто є обов'язковим.";
        }

        $about = trim($data['about'] ?? '');
        if ($about !== '' && mb_strlen($about) > 500) {
            $errors['about'] = 'Максимум 500 символів.';
        }

        return $errors;
    }
}
