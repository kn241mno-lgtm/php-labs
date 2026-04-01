<?php
/**
 * Клас Users — модель користувача
 *
 * Використовується у всіх завданнях ЛР3.
 */

class Users
{
    private static int $nextId = 1;

    public int $id;
    public ?int $parentId = null;
    public string $name;
    public string $login;
    public string $password;

    /**
     * Конструктор — задає початкові значення властивостей
     */
    public function __construct(string $name = '', string $login = '', string $password = '')
    {
        $this->id = self::$nextId++;
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
     * При клонуванні — кастомізація копії
     */
    public function __clone(): void
    {
        $this->parentId = $this->id;
        $this->id = self::$nextId++;
        $this->password = 'qwerty';
    }
}
