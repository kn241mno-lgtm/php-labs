<?php
/**
 * Клас Product — модель товару
 *
 * Використовується у всіх завданнях ЛР3 (варіант 30).
 */

class Product
{
    public string $name;
    public float $price;
    public string $category;

    /**
     * Конструктор — задає початкові значення властивостей
     */
    public function __construct(string $name = '', float $price = 0.0, string $category = '')
    {
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
    }

    /**
     * Виводить інформацію про товар
     */
    public function getInfo(): string
    {
        return "Товар: {$this->name}, Ціна: {$this->price} грн, Категорія: {$this->category}";
    }

    /**
     * При клонуванні — встановлює значення за замовчанням
     */
    public function __clone(): void
    {
        $this->name = 'Новий товар';
        $this->price = 0.0;
        $this->category = 'Без категорії';
    }
}
