# Еволюція: ЛР4 → ЛР5 → ЛР6 → Курсова

> **Призначення:** показати **як одна схема** поступово розвивається по курсу — від чистого DDL у ЛР4 до повноцінного вебзастосунку в курсовій.
> Студент з першої лабораторної бачить картину цілком і проєктує схему **з урахуванням майбутніх вимог**.
>
> **Одна і та сама БД, один і той самий варіант, один і той самий проект** — зростає через 4 роботи.

## Принцип

```
ЛР4 (схема)  →  ЛР5 (CRUD)  →  ЛР6 (auth)  →  Курсова (повний додаток)
    ↓              ↓                ↓                  ↓
  DDL          2 таблиці        session+ACL         15-25 таблиць
                CRUD            +password            + business logic
                                                     + deployment
```

**Ключова ідея:** не переробляйте схему для кожної лабораторної. Спроєктуйте її **один раз у ЛР4** з розрахунку на курсову — і тільки **додавайте нові таблиці** по мірі потреби.

---

## Повний timeline на прикладі v1 Книгарня

### ЛР4 — Проєктування схеми (60-100 балів)

**Ціль:** спроектувати **повну** схему БД.
**Артефакти:**
- DDL (`.sql` файл або міграції)
- ER-діаграма (Mermaid або Draw.io)
- Пояснення кожного constraint

**Що робимо:**
```sql
-- Мінімум для ЛР4 (на 60 балів):
CREATE TABLE users (...);
CREATE TABLE books (...);
CREATE TABLE categories (...);
CREATE TABLE orders (...);
CREATE TABLE order_items (...);

-- Додатково для оцінки 5 (навіть якщо в ЛР4 ще не використовуємо):
CREATE TABLE roles (...);
CREATE TABLE user_roles (...);
CREATE TABLE audit_log (...);
CREATE TABLE authors (...);
CREATE TABLE book_authors (...);
CREATE TABLE reviews (...);
CREATE TABLE cart_items (...);
CREATE TABLE payments (...);
CREATE TABLE coupons (...);
CREATE TABLE addresses (...);
-- ...повний список з schemas.md
```

**Важливо:** таблиці що не використовуватимуться в ЛР4 — все одно додайте з початку. Причини:
1. На ЛР5-ЛР6 не треба буде переробляти схему.
2. Викладач бачить що ви **думали про повну архітектуру**, а не міні-проект.
3. Оцінка 5 вимагає завершеності.

**Чого НЕ робимо:**
- Не пишемо PHP-код (це в ЛР5)
- Не робимо міграції Laravel (якщо ще не почали фреймворк)
- Але: **DDL у форматі міграцій** — бонус (один файл на одну таблицю)

---

### ЛР5 — CRUD на 2-3 сутності (60-100 балів)

**Ціль:** перший working PHP-додаток з читанням/записом у БД.
**Стек:** зазвичай vanilla PHP + PDO (перед переходом на Laravel).

**Що робимо:**
- Створюємо класи моделей для 2-3 таблиць зі схеми ЛР4
- Реалізуємо CRUD операції (Create, Read, Update, Delete)
- Проста форма + таблиця списку

**Приклад структури:**
```
project/
├── classes/
│   ├── Database.php          # PDO singleton
│   └── BaseModel.php         # базовий клас з CRUD методами
├── models/
│   ├── Book.php              # CRUD для books
│   ├── Category.php          # CRUD для categories
│   └── Author.php            # CRUD для authors
├── views/
│   ├── books/
│   │   ├── list.php
│   │   ├── create.php
│   │   └── edit.php
├── controllers/
│   └── BookController.php
└── index.php                 # router
```

**Приклад моделі:**
```php
<?php
class Book extends BaseModel {
    protected $table = 'books';

    public function getAllWithCategory(): array {
        return $this->db->query("
            SELECT b.*, c.name AS category_name
            FROM books b
            LEFT JOIN categories c ON c.id = b.category_id
            WHERE b.deleted_at IS NULL
            ORDER BY b.created_at DESC
        ")->fetchAll();
    }
}
```

**Чого ЩЕ не робимо:**
- Автентифікації (логін) — це в ЛР6
- Ролей і permissions
- Сесій (в мінімальному вигляді — тільки для flash messages)
- Завантаження зображень
- Замовлень з кошиком (складно без auth)

---

### ЛР6 — Авторизація + сесії (60-100 балів)

**Ціль:** додати систему логіну, реєстрації, розділення прав.

**Що робимо:**
- Реєстрація з `password_hash()` (bcrypt)
- Логін з перевіркою через `password_verify()`
- Session-based auth (`$_SESSION['user_id']`)
- Мінімум 2 ролі: `customer` і `admin`
- Розділення доступу: admin бачить all books, customer — тільки published
- Middleware/guard функції перед контролерами

**Що додається до коду:**
```php
<?php
// classes/Auth.php
class Auth {
    public static function login(string $email, string $password): bool {
        $user = (new User())->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function requireAdmin(): void {
        if (!self::check() || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            exit('Access denied');
        }
    }
}
```

**Що додається до схеми (якщо не було з ЛР4):**
- Поле `password_hash` у users (зазвичай уже є)
- Таблиці `password_resets`, `login_attempts`
- **Нічого перероблювати не треба** — якщо схема з ЛР4 відразу мала all needed поля

**Чого ЩЕ не робимо:**
- Повноцінного e-commerce (кошик, замовлення, оплата) — це курсова
- Багатомовність
- REST API

---

### Курсова — Повний застосунок (60-100 балів)

**Ціль:** повноцінний вебсайт з реальним бізнес-процесом.
**Стек:** Laravel 10/11 (рекомендовано) або vanilla PHP MVC (до 80 балів).

**Що робимо:**

#### Обов'язково:
1. **Міграції Laravel** — переписуємо DDL з ЛР4 у формат `php artisan make:migration`
2. **Eloquent моделі** для кожної таблиці
3. **Кошик + замовлення** — якщо Booking/E-commerce категорія
4. **Завантаження зображень** (`storage/app/public` + symlink)
5. **Пагінація** списків
6. **Пошук** (мінімум по одному полю)
7. **Валідація** через Request-класи
8. **Blade templates** з layouts (до 15+ view файлів)
9. **Middleware** для auth + roles
10. **Seeders + Factories** для тестових даних

#### Опціонально (+бали):
- **Email-сповіщення** (Mailtrap для dev)
- **REST API** з Sanctum
- **Експорт у PDF/Excel**
- **DataTables.net** для advanced таблиць
- **Інтеграція з зовнішнім API** (Nova Post, LiqPay, Google Maps)
- **Сповіщення в реальному часі** (Pusher / Laravel Echo)
- **Багатомовність** (`__('messages.hello')`)

**Приклад переходу з ЛР5 у Laravel:**

*Було (vanilla PHP, ЛР5):*
```php
<?php
class Book extends BaseModel {
    protected $table = 'books';
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO books (title, price, category_id)
            VALUES (:title, :price, :category_id)
        ");
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }
}
```

*Стало (Laravel, курсова):*
```php
<?php
namespace App\Models;

class Book extends Model {
    protected $fillable = ['title', 'price', 'category_id', 'isbn'];
    protected $dates = ['deleted_at'];
    use SoftDeletes;

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function authors() {
        return $this->belongsToMany(Author::class, 'book_authors');
    }
}

// У контролері:
$book = Book::create($request->validated());
```

**Ключова різниця з ЛР5:** схема **не міняється**. Тільки код пишемо інакше — декларативно через Eloquent.

---

## Карта зростання схеми

| Артефакт | ЛР4 | ЛР5 | ЛР6 | Курсова |
|----------|-----|-----|-----|---------|
| **users** | ✅ (повна) | 🔵 (read-only, hardcoded user) | ✅ (auth) | ✅ (full RBAC) |
| **books** | ✅ | ✅ CRUD | ✅ CRUD | ✅ CRUD + search + pagination |
| **categories** | ✅ | ✅ CRUD | ✅ | ✅ (nested) |
| **authors** | ✅ | — | — | ✅ |
| **book_authors** | ✅ | — | — | ✅ |
| **orders** | ✅ | — | — | ✅ (повний flow) |
| **order_items** | ✅ | — | — | ✅ |
| **cart_items** | ✅ | — | — | ✅ |
| **addresses** | ✅ | — | — | ✅ |
| **payments** | ✅ | — | — | ✅ (інтеграція з LiqPay) |
| **coupons** | ✅ | — | — | ⚙️ (опціонально) |
| **reviews** | ✅ | — | — | ✅ |
| **wishlist** | ✅ | — | — | ⚙️ |
| **roles** | ✅ | — | ✅ (simple enum) | ✅ (full RBAC) |
| **user_roles** | ✅ | — | — | ✅ |
| **permissions** | ✅ | — | — | ⚙️ |
| **role_permissions** | ✅ | — | — | ⚙️ |
| **audit_log** | ✅ | — | — | ⚙️ |
| **password_resets** | ✅ | — | ✅ | ✅ |
| **login_attempts** | ✅ | — | — | ⚙️ |

**Легенда:**
- ✅ — активно використовується
- 🔵 — існує в схемі, але робота тільки на читання або з хардкодом
- ⚙️ — опціонально для + балів
- — — не використовується (але в схемі є з ЛР4)

---

## Типова помилка студентів

❌ **«Спроектую схему-мінімум на ЛР4, потім переробляю»** — призводить до:
- Втрати балів на ЛР4 (неповна схема)
- Переробки схеми на ЛР5/ЛР6 (стрес)
- Розходження міграцій — одна існує в старому стилі, інша в новому
- На захисті курсової неможливо пояснити чому схема саме така (якщо вона зросла хаотично)

✅ **«Спроектую повну схему на ЛР4, потім наповнюю фічами»** — призводить до:
- Оцінка 5 за ЛР4
- Швидке ЛР5 — просто пишемо CRUD на готових таблицях
- Швидке ЛР6 — таблиці для auth вже є
- На захисті курсової: «я з самого початку бачив куди йду»

---

## Специфіка для різних категорій варіантів

### E-commerce (v1-v4, v8, ...)

**ЛР4:** Users, Products, Categories, Orders, OrderItems, CartItems, Reviews (мін. 7 таблиць для оцінки 5).
**ЛР5:** CRUD на Products + Categories.
**ЛР6:** Auth + admin panel для management products.
**Курсова:** Повний flow — browse catalog → add to cart → checkout → payment → order history.

### Booking (v3, v14, v16, v18, v19, v24)

**ЛР4:** Users, Resources (халли/кабінети), TimeSlots (сеанси/прийоми), Bookings, Status_log.
**ЛР5:** CRUD на Resources + TimeSlots.
**ЛР6:** Auth + доступ до моїх бронювань.
**Курсова:** Повний flow — view availability → pick slot → book → get confirmation → manage.

### Catalog (v5, v6, v7, v26)

**ЛР4:** Users, Items (книги), Copies (примірники), Categories, Loans, Reservations.
**ЛР5:** CRUD на Items + Categories.
**ЛР6:** Auth + персональні видачі.
**Курсова:** Повний flow — browse → reserve → loan → return → fines.

### Service-order (v2, v10, v12, v15, v17, v20, v22, v28, v29)

**ЛР4:** Users, Services, Orders, OrderItems, StatusLog, Pickups.
**ЛР5:** CRUD на Services.
**ЛР6:** Auth + мої замовлення.
**Курсова:** Повний flow — create order → track status → receive notifications → rate service.

### UGC (v9, v30)

**ЛР4:** Users, Posts, Comments, Likes, Tags, PostTags, Follows.
**ЛР5:** CRUD на Posts + Tags.
**ЛР6:** Auth + writing as current user.
**Курсова:** Повний flow — register → write post → comments + likes → follow authors → feed.

---

## Рекомендований порядок дій

**Перший тиждень (перед ЛР4):**
1. Прочитати `schemas.md` для своєї категорії.
2. Прочитати `feature-catalog.md` — зрозуміти які фічі потрібні в курсовій.
3. Скласти **повний** список таблиць (не тільки для ЛР4).

**ЛР4:**
1. Написати DDL для **всіх** таблиць зі списку.
2. Зробити ER-діаграму.
3. Пройти `checklist-lr4.md`.
4. Дати на перевірку + захистити.

**Між ЛР4 і ЛР5:**
1. Перечитати свою схему — чи все логічно.
2. Продумати перші моделі для CRUD.

**ЛР5:**
1. Взяти 2-3 таблиці зі схеми.
2. Написати CRUD через vanilla PHP + PDO.
3. Не чіпати схему.

**ЛР6:**
1. Додати Auth на існуючу users-таблицю.
2. Додати session-based access control.
3. Не чіпати схему.

**Перед курсовою:**
1. Мігрувати DDL у формат Laravel migrations (якщо ще не).
2. Налаштувати Laravel проект.
3. Починати Laravel-специфічні речі (Eloquent, Blade, Breeze).

**Курсова:**
1. Реалізувати всі фічі з `feature-catalog.md` мінімуму.
2. Додати опціональні фічі на додаткові бали.
3. Демонстрація + захист.

---

## Посилання

- Повні схеми: [schemas.md](schemas.md)
- ER-діаграми: [er-diagrams.md](er-diagrams.md)
- Чекліст ЛР4: [checklist-lr4.md](checklist-lr4.md)
- Міграції + Seeders: [migrations-seeders-example.md](migrations-seeders-example.md)
- Фіча-каталог: [feature-catalog.md](feature-catalog.md)
- Archetecture: [system-design.md](system-design.md)
