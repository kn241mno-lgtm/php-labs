# PDO SQLite: "could not find driver"

[← Troubleshooting](README.md) | [← ЛР5](../lr5/assignment.md)

---

## Симптом

При запуску ЛР5 з'являється помилка:

```
DB connection error: could not find driver
```

або

```
Помилка підключення до бази даних.
```

**Причина:** PHP не має увімкненого розширення `pdo_sqlite`. Це розширення потрібне для роботи з SQLite через PDO.

---

## Діагностика

Відкрийте термінал і виконайте:

```powershell
php -m | findstr pdo_sqlite
```

Якщо команда нічого не вивела — розширення не увімкнене.

---

## Рішення

### Крок 1: Знайдіть свій php.ini

```powershell
php --ini
```

Зверніть увагу на рядок `Loaded Configuration File` — це шлях до вашого `php.ini`.

> Якщо написано `(none)` — php.ini не існує. Перейдіть до розділу [php.ini не існує](#phpini-не-існує).

### Крок 2: Увімкніть розширення

Відкрийте `php.ini` у текстовому редакторі (Блокнот, VS Code) і знайдіть рядок (Ctrl+F):

```ini
;extension=pdo_sqlite
```

Приберіть `;` на початку:

```ini
extension=pdo_sqlite
```

> Також рекомендується увімкнути `sqlite3` (якщо є):
>
> ```ini
> extension=sqlite3
> ```

### Крок 3: Перезапустіть сервер

```powershell
# Зупиніть попередній сервер (Ctrl+C) і запустіть заново
php -S localhost:8000
```

### Крок 4: Перевірте

```powershell
php -m | findstr pdo_sqlite
```

Має вивести `pdo_sqlite`.

---

## Варіанти залежно від способу встановлення PHP

### Scoop

```powershell
php --ini
# Зазвичай: C:\Users\<ім'я>\scoop\apps\php\current\php.ini
```

Відкрийте цей файл і виконайте Крок 2.

> Якщо `php.ini` немає — скопіюйте `php.ini-development`:
>
> ```powershell
> cd ~\scoop\apps\php\current
> copy php.ini-development php.ini
> ```
>
> Потім відкрийте `php.ini` і виконайте Крок 2.

### Ручне встановлення (C:\php)

```powershell
php --ini
# Зазвичай: C:\php\php.ini
```

Якщо `php.ini` немає:

```powershell
cd C:\php
copy php.ini-development php.ini
```

Відкрийте `php.ini` і:

1. Знайдіть `extension_dir` — приберіть `;` і переконайтесь що шлях вірний:
   ```ini
   extension_dir = "ext"
   ```
2. Увімкніть `extension=pdo_sqlite` (Крок 2)

### XAMPP

Файл: `C:\xampp\php\php.ini`

Відкрийте і виконайте Крок 2.

> В XAMPP `pdo_sqlite` зазвичай вже увімкнений. Якщо ні — перезапустіть Apache через XAMPP Control Panel після зміни.

### WAMP

Файл: `C:\wamp64\bin\php\php8.x.x\php.ini`

> В WAMP є **два** `php.ini` — один для Apache, один для CLI. Для `php -S` потрібен CLI-версія. Шлях покаже `php --ini`.

---

## php.ini не існує

Якщо `php --ini` показує `Loaded Configuration File: (none)`:

1. Знайдіть папку PHP:
   ```powershell
   where php
   ```
2. У цій папці знайдіть файл `php.ini-development`
3. Скопіюйте його як `php.ini`:
   ```powershell
   copy php.ini-development php.ini
   ```
4. Відкрийте `php.ini` і виконайте Крок 2

---

## Альтернатива: MySQL замість SQLite

Якщо у вас XAMPP/WAMP і ви хочете використати MySQL замість SQLite:

1. Запустіть MySQL через XAMPP/WAMP Control Panel
2. Створіть базу даних `lab5` через phpMyAdmin (`http://localhost/phpmyadmin`)
3. Імпортуйте `database/schema.sql`
4. Змініть `config/database.php`:

```php
return [
    'dsn' => 'mysql:host=localhost;dbname=lab5;charset=utf8',
    'username' => 'root',
    'password' => '',
];
```

> При використанні MySQL потрібне розширення `pdo_mysql` (зазвичай увімкнене в XAMPP/WAMP за замовчуванням).

---

## Перевірка що все працює

```powershell
php -m | findstr pdo_sqlite   # має вивести pdo_sqlite
php -S localhost:8000          # запуск сервера
```

Відкрийте у браузері сторінку з базою даних — помилка має зникнути.
