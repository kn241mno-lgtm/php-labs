# Лабораторна робота №6

**Тема:** Знайомство з Laravel: встановлення, конфігурація, робота з БД та HTTP. Eloquent ORM, Auth, CRUD.

**Джерело:** [laravel_lab6.pdf](https://learn.ztu.edu.ua/pluginfile.php/462165/mod_resource/content/1/laravel_lab6.pdf) · локальна копія: [LARAVEL LAB 6.pdf](LARAVEL%20LAB%206.pdf)

## Мета роботи

Навчитися встановлювати та налаштовувати фреймворк Laravel, розуміти архітектуру MVC, працювати з міграціями та Eloquent ORM, реалізовувати автентифікацію, повний CRUD для моделей та завантаження файлів.

## Необхідне ПЗ

- PHP **8.1+**
- Composer
- MySQL / MariaDB
- Node.js + npm (збірка фронтенду)
- phpMyAdmin або DBeaver
- VS Code або PhpStorm

---

## Частина 1 — Базовий додаток (50 балів)

### Завдання 1: Встановлення Laravel

1. Перевірити: `php -v`, `composer -V`
2. Створити проєкт: `composer create-project laravel/laravel laravel-app`
3. Запустити: `cd laravel-app && php artisan serve`
4. Відкрити `http://127.0.0.1:8000` — має з'явитися стартова сторінка
5. Зробити скриншот стартової сторінки для звіту

### Завдання 2: Налаштування `.env` і підключення БД

1. Відкрити `.env` у корені проєкту
2. Створити БД `laravel_db` у phpMyAdmin
3. Заповнити `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. Виконати `php artisan migrate` — без помилок
5. Перевірити у phpMyAdmin: таблиці `users`, `migrations` тощо створились

> ⚠️ Файл `.env` НЕ публікувати у відкритий репозиторій. Він у `.gitignore` автоматично.

### Завдання 3: Міграції та схема БД

1. Вивчити міграцію для `users`
2. Перевірити наявність полів: `name`, `email` (unique), `password` (hash), `age` (nullable), `role` (default 'user'), `phone` (nullable)
3. Виконати `php artisan migrate`
4. Перевірити структуру таблиці `users` у phpMyAdmin
5. Пояснити у звіті: чому пароль зберігається як хеш, а не відкритим текстом

### Завдання 4: Eloquent ORM

1. Створити модель: `php artisan make:model Product -m`
2. Додати у міграцію поля: `name`, `category`, `price`, `description`, `image`
3. Виконати `php artisan migrate`
4. У моделі заповнити `$fillable` (захист від Mass Assignment)
5. Протестувати через `php artisan tinker`: `Product::all()`, `Product::create([...])`

**Ключові операції:** `Product::all()`, `find(id)`, `findOrFail(id)`, `create([...])`, `update([...])`, `delete()`, `where(...)->get()`

### Завдання 5: Базовий додаток з CRUD

**Вимоги:**

| Функція | Деталі |
|---------|--------|
| Авторизація | Laravel Breeze, лише вхід (реєстрацію видалити з `routes/auth.php`) |
| Seeder | `AdminSeeder` → `admin@admin.com` / `password`, `role='admin'` |
| CRUD Users | index, create, store, edit, update, destroy |
| CRUD Products | index, create, store, show, edit, update, destroy |
| Поля Users | ПІБ (required), email, password, вік, роль, телефон |
| Поля Products | Назва (required), категорія (list), ціна, опис, зображення (мін. 100×100) |
| Міграції | Усі таблиці через міграції |
| Зображення | `storage/app/public` + `php artisan storage:link` (symlink) |
| Контролери | Resource controllers: `php artisan make:controller ProductController --resource` |
| Маршрути | `Route::resource('products', ProductController::class)->middleware('auth')` |

**Кроки:**

1. Встановити Breeze: `composer require laravel/breeze --dev` → `php artisan breeze:install` → `npm install && npm run dev` → `php artisan migrate`
2. Видалити маршрути `register` у `routes/auth.php`
3. Створити `AdminSeeder` → `php artisan db:seed --class=AdminSeeder` → перевірити вхід
4. Створити `ProductController --resource` та заповнити методи
5. Реалізувати upload зображень через `storage` з валідацією `min_dimensions:100,100`
6. Зареєструвати `Route::resource` з `middleware('auth')`
7. Протестувати CRUD повністю

---

## Частина 2 — Розширений функціонал (50 балів)

### Завдання 6: Invoices, Request-класи, пагінація, DataTables, Email, i18n, AdminLTE

**Вимоги:**

| Функція | Технологія/Деталі |
|---------|------------------|
| Invoices | ID, `user_id` (FK), `product_id` (FK), `invoice_date`, `quantity`, `total_amount` |
| Зв'язки | `belongsTo` (Invoice→User, Invoice→Product), `hasMany` (User→Invoices) |
| Валідація | Окремі Request-класи: `php artisan make:request StoreProductRequest` |
| Пагінація | 10 записів/сторінку — `paginate(10)` + `{{ $items->links() }}` |
| DataTables | [Datatables.net](https://datatables.net) — пошук, сортування, фільтрація |
| Email | SMTP через [Mailtrap](https://mailtrap.io) — лист при створенні User |
| i18n | `resources/lang/uk/`, `resources/lang/en/` — мінімум 2 мови + перемикач |
| Тема | [AdminLTE](https://adminlte.io) або аналог Bootstrap-адмін-теми |

**Кроки:**

1. Міграція + модель `Invoice` з FK через `foreignId()->constrained()->onDelete('cascade')`
2. Додати `belongsTo`/`hasMany` у `Invoice`, `User`, `Product`
3. CRUD для Invoices з автоматичним розрахунком `total_amount = quantity × price`
4. Винести валідацію Products/Users у окремі `FormRequest`-класи
5. Замінити `Model::all()` на `paginate(10)` у всіх контролерах, додати `{{ $items->links() }}`
6. Підключити DataTables до таблиць Users, Products, Invoices
7. Зареєструватися на Mailtrap, налаштувати `MAIL_*` у `.env`, створити `UserCreatedMail`, надсилати через `Mail::to($user->email)->send(...)`
8. Додати мінімум 2 мови (`uk`, `en`), реалізувати перемикач у навігації, використовувати `{{ __('messages.key') }}`
9. Встановити AdminLTE: `composer require jeroennoten/laravel-adminlte` → `php artisan adminlte:install` → адаптувати шаблони

> 💡 **Eager loading проти N+1:** `Invoice::with(['user', 'product'])->paginate(10)` — щоб уникнути окремих SQL-запитів на кожен рядок.

---

## Критерії оцінювання

### Частина 1 — Базовий додаток (50 балів)

| # | Критерій | Балів | Перевірка |
|---|----------|-------|-----------|
| 1 | Laravel встановлено, стартова сторінка відкривається | 5 | Скриншот браузера |
| 2 | `.env` налаштований, `php artisan migrate` без помилок | 5 | Скриншот phpMyAdmin |
| 3 | Міграції для `users` і `products` створені правильно | 5 | Структура таблиць |
| 4 | Моделі з `$fillable` та Eloquent-операції працюють | 5 | `php artisan tinker` |
| 5 | Авторизація через Breeze, вхід `admin@admin.com`/`password` | 10 | Демо входу |
| 6 | CRUD Products повністю реалізований | 10 | Тестування форм |
| 7 | Завантаження зображень (storage + symlink) | 5 | Зображення відображається |
| 8 | Маршрути через `Route::resource` з `middleware('auth')` | 5 | `php artisan route:list` |

### Частина 2 — Розширений функціонал (50 балів)

| # | Критерій | Балів | Перевірка |
|---|----------|-------|-----------|
| 1 | Таблиця `invoices` з FK, зв'язки між моделями | 8 | Схема БД + код |
| 2 | Request-класи для валідації Products/Users | 7 | Код Request-класу |
| 3 | Пагінація (10 записів) у всіх списках | 5 | Навігація по сторінках |
| 4 | DataTables: пошук та сортування | 8 | Демо пошуку |
| 5 | Email при створенні User (Mailtrap) | 8 | Скриншот листа у Mailtrap |
| 6 | Багатомовність (мін. 2 мови, перемикач) | 7 | Демо перемикання |
| 7 | Адмін-тема (AdminLTE або аналог) | 7 | Зовнішній вигляд |

**Разом: 100 балів**

---

## Корисні ресурси

| Ресурс | URL | Що знайти |
|--------|-----|-----------|
| Laravel Docs | [laravel.com/docs](https://laravel.com/docs) | Офіційна документація |
| Laravel Breeze | [laravel.com/docs/starter-kits](https://laravel.com/docs/starter-kits) | Стартовий кіт авторизації |
| Eloquent ORM | [laravel.com/docs/eloquent](https://laravel.com/docs/eloquent) | Робота з БД |
| Migrations | [laravel.com/docs/migrations](https://laravel.com/docs/migrations) | Міграції |
| Validation | [laravel.com/docs/validation](https://laravel.com/docs/validation) | Валідація + Request-класи |
| Pagination | [laravel.com/docs/pagination](https://laravel.com/docs/pagination) | Пагінація |
| Mail | [laravel.com/docs/mail](https://laravel.com/docs/mail) | Надсилання email |
| Localization | [laravel.com/docs/localization](https://laravel.com/docs/localization) | Багатомовність |
| Mailtrap | [mailtrap.io](https://mailtrap.io) | Тестування email (SMTP sandbox) |
| DataTables | [datatables.net](https://datatables.net) | Інтерактивні таблиці |
| AdminLTE | [adminlte.io](https://adminlte.io) | Адмін-тема |
| Laracasts | [laracasts.com](https://laracasts.com) | Відеоуроки з Laravel |

> 📖 Повні інструкції з кодом, поясненнями та скриншотами — у PDF методички (див. секцію «Джерело» вище).
