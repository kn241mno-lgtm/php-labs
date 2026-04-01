<?php

class SettingsController extends PageController
{
    private array $availableColors = [
        '#FFF8E1' => 'Ваніль',
        '#E8F5E9' => 'М\'ятний',
        '#FFCDD2' => 'Полуничний',
        '#BBDEFB' => 'Чорничний',
        '#E1BEE7' => 'Лавандовий',
        '#C8E6C9' => 'Базилік',
        '#FFAB91' => 'Гарбузовий',
        '#B2EBF2' => 'Крижаний',
        '#F0F4C3' => 'Лимонний',
        '#D7CCC8' => 'Какао',
        '#CFD8DC' => 'Сталевий',
        '#FFFFFF' => 'Білий',
    ];

    public function action_color(): void
    {
        $message = '';
        $messageType = 'success';

        if ($this->request->isPost()) {
            $color = $this->request->postString('bg_color', '#FFF8E1');

            if (array_key_exists($color, $this->availableColors)) {
                $_SESSION['bg_color'] = $color;
                $message = 'Колір фону збережено!';
            } else {
                $message = 'Невідомий колір.';
                $messageType = 'error';
            }
        }

        $this->render('settings/color', [
            'colors' => $this->availableColors,
            'currentColor' => $_SESSION['bg_color'] ?? '#FFF8E1',
            'message' => $message,
            'messageType' => $messageType,
        ], 'Колір фону');
    }

    public function action_greeting(): void
    {
        $message = '';
        $messageType = 'success';

        if ($this->request->isPost()) {
            $name = trim($this->request->postString('greeting_name'));
            $gender = $this->request->postString('greeting_gender');

            if ($name === '') {
                $message = "Ім'я не може бути порожнім.";
                $messageType = 'error';
            } elseif (!in_array($gender, ['male', 'female'], true)) {
                $message = 'Оберіть стать.';
                $messageType = 'error';
            } else {
                $cookieOptions = [
                    'expires' => time() + 30 * 24 * 3600,
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ];
                setcookie('greeting_name', $name, $cookieOptions);
                setcookie('greeting_gender', $gender, $cookieOptions);

                $_COOKIE['greeting_name'] = $name;
                $_COOKIE['greeting_gender'] = $gender;

                $message = 'Привітання збережено!';
            }
        }

        $this->render('settings/greeting', [
            'message' => $message,
            'messageType' => $messageType,
            'currentName' => $_COOKIE['greeting_name'] ?? '',
            'currentGender' => $_COOKIE['greeting_gender'] ?? '',
        ], 'Привітання (Cookie)');
    }
}
