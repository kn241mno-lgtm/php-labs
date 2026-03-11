<?php

class SettingsController extends PageController
{
    private array $availableColors = [
        '#FFFAF0' => 'Ваніль',
        '#FFF8DC' => 'Кукурудзяний',
        '#F5F5DC' => 'Бежевий',
        '#FAEBD7' => 'Античний білий',
        '#FFE4C4' => 'Бісквітний',
        '#FFDAB9' => 'Персиковий',
    ];

    public function action_color(): void
    {
        $message = '';

        if ($this->request->isPost()) {
            $color = $this->request->post('bg_color', '#FFFAF0');

            if (array_key_exists($color, $this->availableColors)) {
                $_SESSION['bg_color'] = $color;
                $message = 'Колір фону збережено!';
            } else {
                $message = 'Невідомий колір.';
            }
        }

        $this->render('settings/color', [
            'colors' => $this->availableColors,
            'currentColor' => $_SESSION['bg_color'] ?? '#FFFAF0',
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

                $message = 'Привітання збережено!';
            }
        }

        $this->render('settings/greeting', [
            'message' => $message,
            'currentName' => $_COOKIE['greeting_name'] ?? '',
            'currentGender' => $_COOKIE['greeting_gender'] ?? '',
        ], 'Привітання (Cookie)');
    }
}
