# Лабораторна робота №3

**Тема:** ООП в PHP

## Теорія

[W3Schools — PHP OOP](https://www.w3schools.com/php/php_oop_what_is.asp) (класи, об'єкти, конструктори, клонування)

## Як виконувати

1. Прочитайте свій варіант: `variants/vN.md` (де N — ваш номер)
2. Подивіться робочий приклад: `demo/` — запустіть `php -S localhost:8000` і відкрийте `http://localhost:8000/lr3/demo/`
3. Подивіться повністю розв'язаний варіант 30: `variants/v30/` — `http://localhost:8000/lr3/variants/v30/`
4. Створіть файли у папці `lr3/` (дивіться demo/ для прикладу структури)

**Де створювати файли:**

```text
lr3/
├── demo/           ← НЕ ЧІПАТИ (приклад від викладача)
├── variants/
│   ├── v1.md ... v30.md  ← завдання (НЕ ЧІПАТИ)
│   └── v30/            ← повний приклад (НЕ ЧІПАТИ)
├── YourClass.php   ← ваш клас (назва з варіанту)
├── task1.php       ← завдання 1
├── task2.php       ← завдання 2
├── task3.php       ← завдання 3
└── task4.php       ← завдання 4
```

**Як перевірити:** запустіть `php -S localhost:8000` і відкрийте `http://localhost:8000/lr3/task1.php`

## Завдання

### 1. Створення класів та об'єктів

- Клас `Users` із властивостями: `name`, `login`, `password`
- Створити 3 об'єкти з довільними значеннями

### 2. Методи об'єкта

- Метод `getInfo()` — виводить значення властивостей
- Викликати для кожного об'єкта

### 3. Конструктор

- Конструктор задає початкові значення `name`, `login`, `password`
- Перестворити 3 об'єкти через конструктор

### 4. Клонування

- `clone` створює точну копію об'єкта (всі властивості копіюються)
- Метод `__clone()` — викликається автоматично після копіювання, дозволяє кастомізувати копію
- У `__clone()` скинути пароль на значення за замовчанням: `password = "qwerty"`
- Створити 4-й об'єкт через `clone`

## Корисні посилання

- [PHP OOP](https://www.w3schools.com/php/php_oop_what_is.asp) — що таке ООП
- [PHP Classes/Objects](https://www.w3schools.com/php/php_oop_classes_objects.asp) — класи та об'єкти
- [PHP Constructor](https://www.w3schools.com/php/php_oop_constructor.asp) — конструктор
- [PHP Access Modifiers](https://www.w3schools.com/php/php_oop_access_modifiers.asp) — модифікатори доступу
- [PHP Object Cloning](https://www.php.net/manual/en/language.oop5.cloning.php) — клонування (php.net)

## Здача

- Гілка `lr3` в репозиторії (див. [acceptance-criteria.md](../docs/acceptance-criteria.md))
- Push на GitHub
