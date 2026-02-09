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
        '#ffedd5' => 'Помаранчевий',
        '#ffffff' => 'Білий',
    ];

    public function action_color(): void
    {
        $message = '';

        if ($this->request->isPost()) {
            $color = $this->request->post('bg_color', '#f9fafb');

            if (array_key_exists($color, $this->availableColors)) {
                $_SESSION['bg_color'] = $color;
                $message = 'Колір фону збережено!';
            } else {
                $message = 'Невідомий колір.';
            }
        }

        $this->render('settings/color', [
            'colors' => $this->availableColors,
            'currentColor' => $_SESSION['bg_color'] ?? '#f9fafb',
            'message' => $message,
        ], 'Колір фону');
    }

    public function action_greeting(): void
    {
        $message = '';

        if ($this->request->isPost()) {
            $name = trim($this->request->post('greeting_name', ''));
            $gender = $this->request->post('greeting_gender', '');

            if ($name === '') {
                $message = "Ім'я не може бути порожнім.";
            } elseif (!in_array($gender, ['male', 'female'], true)) {
                $message = 'Оберіть стать.';
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
            'currentName' => $_COOKIE['greeting_name'] ?? '',
            'currentGender' => $_COOKIE['greeting_gender'] ?? '',
        ], 'Привітання (Cookie)');
    }
}
