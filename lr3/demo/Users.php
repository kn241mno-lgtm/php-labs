<?php
/**
 * Клас Users — модель користувача
 *
 * Використовується у всіх завданнях ЛР3.
 */

class Users
{
    public string $name;
    public string $login;
    public string $password;

    /**
     * Конструктор — задає початкові значення властивостей
     */
    public function __construct(string $name = '', string $login = '', string $password = '')
    {
        $this->name = $name;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Виводить інформацію про користувача
     */
    public function getInfo(): string
    {
        return "Ім'я: {$this->name}, Логін: {$this->login}, Пароль: {$this->password}";
    }

    /**
     * При клонуванні — встановлює значення за замовчанням
     */
    public function __clone(): void
    {
        $this->name = 'User';
        $this->login = 'User';
        $this->password = 'qwerty';
    }
}
