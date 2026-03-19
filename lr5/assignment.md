# Лабораторна робота №5

**Тема:** Робота з файлами та базою даних MySQL (PDO)

## Теорія

- [PHP File Handling](https://www.w3schools.com/php/php_file.asp) — робота з файлами
- [PHP File Upload](https://www.w3schools.com/php/php_file_upload.asp) — завантаження файлів
- [PHP MySQL (PDO)](https://www.w3schools.com/php/php_mysql_intro.asp) — бази даних
- [PHP PDO](https://www.php.net/manual/en/book.pdo.php) — документація PDO

## Як виконувати

1. Подивіться робочий приклад: `demo/` — запустіть `php -S localhost:8080` з папки `lr5/demo/`
2. Скопіюйте MVC-структуру з LR4 або з demo/ і розширіть її новими модулями
3. Для БД: demo використовує SQLite (файл `database/app.db`). В університеті можете використати MySQL — змініть DSN в `config/database.php`

**Як перевірити:** `cd lr5/demo && php -S localhost:8080` → `http://localhost:8080`

> Ця лабораторна розширює LR4 (MVC). Ваша MVC-архітектура (Application, Router, Controller, View) залишається — додаються нові контролери та модулі.

## Завдання 1: Робота з файлами

### 1.1 Гостьова книга

- Створіть форму з полями **Ім'я** та **Коментар**
- Збережіть дані у текстовий файл (1 коментар = 1 рядок, роздільник — `|`)
- На цій же сторінці виведіть всі коментарі з файлу в таблицю
- Додайте дату/час кожного коментаря

### 1.2 Завантаження зображень

- Створіть форму для завантаження зображень на сервер
- Після відправки форми зображення має бути прийняте PHP-скриптом, перевірене (тип, розмір) та збережене в каталозі `data/uploads/`
- Виведіть галерею всіх завантажених зображень

### 1.3 Робота з каталогами

- Створіть форму з полями **Логін** та **Пароль**
- При відправці створіть папку з ім'ям логіна (якщо не існує), всередині — підпапки `video`, `music`, `photo` + по 2-3 файли в кожній
- Якщо папка вже існує — повідомлення про помилку
- На окремій сторінці `delete` — форма для видалення папки за логіном (з усім вмістом)

## Завдання 2: База даних (PDO)

### 2.1 Підключення та CRUD

- Створіть базу даних та таблицю для сутності за темою вашого сайту (товари, послуги, рецепти тощо — мінімум 5 полів)
- Підключіться через PDO (`try/catch` для обробки помилок)
- Реалізуйте на окремих сторінках:
  - Виведення всіх записів у таблиці
  - Додавання нового запису (форма)
  - Редагування існуючого запису (форма з передзаповненням)
  - Видалення запису за ID
- Використовуйте **prepared statements** (`prepare` + `execute`) для всіх запитів

### 2.2 Авторизація через БД

- Таблиця `users` (мінімум 10 полів: id, login, password, email, first_name, last_name, phone, city, about, created_at)
- Форма реєстрації з валідацією (унікальний логін, пароль ≥ 6 символів, email формат)
- Форма входу (логін + пароль) — перевірка в БД, сесія для автентифікації
- На всіх сторінках: якщо авторизований — привітання + посилання на профіль/вихід, інакше — посилання на вхід/реєстрацію
- Хешування паролів (`password_hash` / `password_verify`)

### 2.3 Профіль користувача

- Сторінка профілю з усіма даними з БД
- Можливість редагування своїх даних (зміни зберігаються в `users`)
- Можливість видалення акаунту
- Вихід (знищення сесії)

## Очікувана структура

```
lr5/
├── classes/
│   ├── Application.php
│   ├── Controller.php
│   ├── PageController.php
│   ├── Request.php
│   ├── Router.php
│   ├── View.php
│   ├── PageView.php
│   └── Database.php          # NEW: PDO singleton
├── config/
│   ├── init.php
│   └── database.php          # NEW: DSN config
├── controllers/
│   ├── IndexController.php
│   ├── SettingsController.php # LR4: session + cookie
│   ├── GuestbookController.php # NEW: файлова гостьова книга
│   ├── UploadController.php    # NEW: завантаження зображень
│   ├── FolderController.php    # NEW: робота з каталогами
│   ├── CatalogController.php   # NEW: CRUD з PDO
│   └── AuthController.php      # NEW: реєстрація/вхід/профіль
├── views/
│   ├── layout/ (header.php, footer.php)
│   ├── index/ (main.php)
│   ├── settings/ (color.php, greeting.php)
│   ├── guestbook/ (index.php)
│   ├── upload/ (index.php)
│   ├── folder/ (create.php, delete.php)
│   ├── catalog/ (list.php, create.php, edit.php)
│   └── auth/ (login.php, register.php, profile.php, edit.php)
├── data/
│   ├── comments.txt
│   ├── uploads/
│   └── users/
├── database/
│   ├── schema.sql
│   └── app.db
├── css/
│   └── style.css
├── index.php
└── .htaccess
```

## Здача

- Гілка `lr5` в репозиторії (див. [acceptance-criteria.md](../docs/acceptance-criteria.md))
- Push на GitHub

## Корисні посилання

- [PHP File Create/Write](https://www.w3schools.com/php/php_file_create.asp) — створення та запис файлів
- [PHP File Open/Read](https://www.w3schools.com/php/php_file_open.asp) — читання файлів
- [PHP File Upload](https://www.w3schools.com/php/php_file_upload.asp) — завантаження файлів
- [PHP Directories](https://www.w3schools.com/php/php_ref_directory.asp) — функції каталогів
- [PHP MySQL Connect](https://www.w3schools.com/php/php_mysql_connect.asp) — підключення до MySQL
- [PHP MySQL Insert](https://www.w3schools.com/php/php_mysql_insert.asp) — вставка даних
- [PHP MySQL Select](https://www.w3schools.com/php/php_mysql_select.asp) — вибірка даних
- [PHP MySQL Prepared](https://www.w3schools.com/php/php_mysql_prepared_statements.asp) — prepared statements
- [PHP password_hash](https://www.php.net/manual/en/function.password-hash.php) — хешування паролів
- [PDO Tutorial](https://www.php.net/manual/en/pdo.connections.php) — PDO підключення
