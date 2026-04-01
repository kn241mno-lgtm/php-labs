<?php
/**
 * Клас Product — модель товару
 *
 * Використовується у всіх завданнях ЛР3 (варіант 30).
 */

class Product
{
    private static int $nextId = 1;

    public int $id;
    public ?int $parentId = null;
    public string $name;
    public float $price;
    public string $category;

    /**
     * Конструктор — задає початкові значення властивостей
     */
    public function __construct(string $name = '', float $price = 0.0, string $category = '')
    {
        $this->id = self::$nextId++;
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
     * При клонуванні — кастомізація копії
     */
    public function __clone(): void
    {
        $this->parentId = $this->id;
        $this->id = self::$nextId++;
        $this->price = 0.0;
    }
}
