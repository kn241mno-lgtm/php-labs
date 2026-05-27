# Типові помилки проєктування БД (і як втрачають бали)

> **Призначення:** каталог антипатернів з LR4 та курсових.
> Для кожної помилки — приклад «як погано», «як правильно», і **скільки балів втрачають**.
> Читайте перед здачею — відсіє 80% поширених проблем.

## Формат

```
### Назва помилки
❌ Як не треба
✅ Як правильно
💸 Мінус N балів на захисті
🧠 Чому це погано
```

---

## 1. Типи даних

### 1.1. VARCHAR(255) всюди за замовчуванням

❌ Погано:
```sql
CREATE TABLE users (
    email VARCHAR(255),
    phone VARCHAR(255),
    country_code VARCHAR(255),
    currency_code VARCHAR(255)
);
```

✅ Правильно:
```sql
CREATE TABLE users (
    email VARCHAR(255),      -- ок, стандарт RFC 5321
    phone VARCHAR(20),       -- максимум E.164 — 15 цифр + плюс
    country_code CHAR(2),    -- ISO 3166-1 alpha-2 (UA, US)
    currency_code CHAR(3)    -- ISO 4217 (UAH, USD)
);
```

💸 **-3 бали** — викладач запитає «чому 255?» і студент не знатиме.

🧠 VARCHAR(255) — дефолт з MySQL 4.x, коли максимум для індексованого поля був 255 байт. Зараз це «я не думав». Вибирайте N за реальним бізнес-обмеженням.

---

### 1.2. FLOAT/DOUBLE для грошей

❌ Погано:
```sql
CREATE TABLE products (
    price FLOAT,
    total DOUBLE
);
-- 0.1 + 0.2 = 0.30000000000000004 — втрата копійок при сумуванні
```

✅ Правильно:
```sql
CREATE TABLE products (
    price DECIMAL(10,2),   -- до 99 999 999.99
    total DECIMAL(12,2)    -- більший запас для сум
);
```

💸 **-5 балів** — це класичний bug, на захисті обов'язково помітять.

🧠 FLOAT/DOUBLE — бінарне IEEE 754 представлення. Десяткові дроби (0.1, 0.2) не представляються точно → копійки «губляться» при арифметиці. DECIMAL зберігає число як рядок digits → точність гарантована.

---

### 1.3. VARCHAR для boolean

❌ Погано:
```sql
is_active VARCHAR(5),  -- 'true'/'false' або 'yes'/'no'
```

✅ Правильно:
```sql
is_active BOOLEAN NOT NULL DEFAULT TRUE,
-- або TINYINT(1) — це те саме в MySQL
```

💸 **-2 бали** + риторичне питання «а якщо хтось вставить 'YES' замість 'yes'?».

🧠 VARCHAR дозволяє будь-який рядок → можна вставити 'maybe'. BOOLEAN обмежує до 0/1.

---

### 1.4. DATETIME vs TIMESTAMP — неправильний вибір

❌ Погано:
```sql
created_at DATETIME DEFAULT NOW(),
-- проблема: при зміні часового поясу бази дата не перерахується
```

✅ Правильно:
```sql
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- для юзер-даних які мають зберігатися в локальному часі:
event_date DATE,           -- день народження — DATE (без часу)
appointment_at DATETIME,   -- зустріч о 15:00 в локальному часі
```

💸 **-2 бали** — і довге пояснення різниці.

🧠 TIMESTAMP зберігається як UTC, конвертується на льоту → правильний для технічних полів. DATETIME — «as-is» без конверсії → для коли важливий локальний час (день народження — 5 травня завжди 5 травня, незалежно від TZ).

---

### 1.5. utf8 замість utf8mb4

❌ Погано:
```sql
CREATE TABLE posts (
    title VARCHAR(200),
    body TEXT
) DEFAULT CHARSET=utf8;
-- "Моя піца 🍕" → emoji зламає insert
```

✅ Правильно:
```sql
CREATE TABLE posts (
    title VARCHAR(200),
    body TEXT
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

💸 **-3 бали** + демонстрація emoji на захисті.

🧠 MySQL `utf8` — це 3-byte UTF-8 (legacy, неправильна назва). `utf8mb4` — повний 4-byte UTF-8 з підтримкою emoji, деяких китайських ієрогліфів.

---

## 2. Ключі та зв'язки

### 2.1. Немає FOREIGN KEY constraint на `_id` полях

❌ Погано:
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,         -- немає FK!
    product_id INT       -- немає FK!
);
-- можна вставити order з user_id=9999 коли такого користувача немає
```

✅ Правильно:
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);
```

💸 **-10 балів** (!!!) — це найбільша втрата. Цілісність даних = базова вимога.

🧠 Без FK → orphaned records (замовлення на неіснуючого користувача). Застосунок може мати bug → БД мала б це прикрити, але не прикриває.

---

### 2.2. ON DELETE не вказано

❌ Погано:
```sql
FOREIGN KEY (user_id) REFERENCES users(id)
-- MySQL за замовчуванням = NO ACTION (== RESTRICT) — але це неявно, викладач запитає
```

✅ Правильно:
```sql
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
```

💸 **-3 бали** — за кожним FK треба розуміти наслідки DELETE.

🧠 Студент має пояснити для **кожного** FK: чому саме CASCADE/RESTRICT/SET NULL. Orders → order_items = CASCADE (замовлення видаляється → позиції теж). Users → orders = RESTRICT (не можна видалити юзера з активними замовленнями).

---

### 2.3. Junction table з сурогатним id замість composite PK

❌ Погано:
```sql
CREATE TABLE book_authors (
    id INT PRIMARY KEY AUTO_INCREMENT,  -- непотрібний сурогат
    book_id INT,
    author_id INT,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (author_id) REFERENCES authors(id)
);
-- можна вставити (book_id=5, author_id=10) двічі — дубль!
```

✅ Правильно:
```sql
CREATE TABLE book_authors (
    book_id INT NOT NULL,
    author_id INT NOT NULL,
    author_order INT NOT NULL DEFAULT 1,  -- корисний додатковий атрибут
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE RESTRICT
);
```

💸 **-5 балів** + «а що якщо вставити двічі?».

🧠 Composite PK автоматично гарантує унікальність пари. Сурогатний id нічого не дає для junction (ніхто не звертається до `book_authors.id` ззовні).

---

### 2.4. Немає UNIQUE на критичних полях

❌ Погано:
```sql
CREATE TABLE users (
    email VARCHAR(255)  -- можна зареєструвати 10 юзерів з одним email
);
```

✅ Правильно:
```sql
CREATE TABLE users (
    email VARCHAR(255) NOT NULL UNIQUE
);
```

💸 **-5 балів** — демонстрація INSERT двох users з одним email.

🧠 Race condition: два паралельних `CHECK + INSERT` в коді → обидва проходять → дубль. UNIQUE constraint на рівні БД — єдина гарантія.

---

### 2.5. Немає UNIQUE на бізнес-ключах у Booking (!)

❌ Погано:
```sql
CREATE TABLE tickets (
    id INT PK,
    session_id INT,
    seat_id INT
    -- нічого не забороняє купити той же seat на той же session двічі
);
```

✅ Правильно:
```sql
CREATE TABLE tickets (
    id INT PK AUTO_INCREMENT,
    session_id INT NOT NULL,
    seat_id INT NOT NULL,
    FOREIGN KEY (session_id) REFERENCES sessions(id),
    FOREIGN KEY (seat_id) REFERENCES seats(id),
    UNIQUE KEY uq_session_seat (session_id, seat_id)  -- 🔥 критично
);
```

💸 **-10 балів** на booking-варіантах — race condition буквально ламає бізнес.

🧠 Без UNIQUE: два користувачі одночасно натиснули «купити» на те саме крісло → application code перевіряє «чи вільно?» → обидві перевірки бачать «вільно» → обидва INSERT проходять → клієнт на касі сидить на колінах у чужого.

---

## 3. Індекси

### 3.1. Немає індексів на FK

❌ Погано:
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
    -- індекс треба окремо
);
-- SELECT * FROM orders WHERE user_id = 5 → FULL SCAN на 1М записів
```

✅ Правильно:
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_id (user_id)
);
-- SELECT * FROM orders WHERE user_id = 5 → INDEX LOOKUP
```

💸 **-5 балів** + питання «як працюватиме при 1 млн записів?».

🧠 InnoDB **автоматично** створює індекс на FK, але не на всіх сторонніх БД → краще вказати явно для читабельності. Для composite queries — окремий composite index.

---

### 3.2. Composite index з неправильним порядком

❌ Погано:
```sql
INDEX idx_created_category (created_at, category_id)
-- для запиту WHERE category_id = 5 ORDER BY created_at DESC — індекс не використається
```

✅ Правильно:
```sql
INDEX idx_category_created (category_id, created_at DESC)
-- правило: спочатку поля з `=`, потім `ORDER BY`
```

💸 **-3 бали** — питання «покажи EXPLAIN».

🧠 Composite index працює zліва-направо: `(a, b, c)` покриває `WHERE a=...`, `WHERE a=... AND b=...`, але не `WHERE b=...`.

---

### 3.3. Індекси на ВСЕ підряд

❌ Погано:
```sql
-- 15 індексів на таблицю з 8 полів:
INDEX idx_email, INDEX idx_phone, INDEX idx_first_name,
INDEX idx_last_name, INDEX idx_birthdate, INDEX idx_city, ...
```

✅ Правильно:
```sql
-- тільки те що реально шукається:
INDEX idx_email (email),           -- логін
INDEX idx_phone (phone),           -- пошук для підтримки
FULLTEXT INDEX ft_name (first_name, last_name)  -- для fuzzy search
```

💸 **-3 бали** — питання «скільки індексів на таблицю?».

🧠 Кожен INSERT/UPDATE → оновлення ВСІХ індексів. 15 індексів = 15x повільніші write. Індекси — для селективних колонок з frequent WHERE/JOIN.

---

## 4. Паролі та безпека

### 4.1. Password plaintext або слабкий хеш

❌ Погано:
```sql
password VARCHAR(50),            -- plaintext
-- або
password CHAR(32),               -- MD5 — зламається за секунди
password CHAR(40),               -- SHA1 — трохи краще, але теж зламається
```

✅ Правильно:
```sql
password_hash VARCHAR(255) NOT NULL COMMENT 'bcrypt (60) or argon2 (97+)',
-- а в PHP: password_hash($pw, PASSWORD_BCRYPT) або PASSWORD_ARGON2ID
```

💸 **-15 балів** (!!!) або повернення курсової на доопрацювання. Plaintext password — грубе порушення.

🧠 Хеш-функція має бути повільна (bcrypt/argon2) — щоб brute force коштував мільярди доларів. MD5 на сучасному GPU — 30 GH/s = весь пароль 6 символів за секунди.

---

### 4.2. Збереження повного номера картки

❌ Погано:
```sql
card_number VARCHAR(19),  -- 1234 5678 9012 3456
cvv CHAR(3),              -- PCI DSS порушення
```

✅ Правильно:
```sql
-- Взагалі НЕ зберігайте — використайте Stripe/LiqPay токенізацію
payment_token VARCHAR(255),  -- токен від платіжки
card_last_4 CHAR(4),          -- для UI "**** 3456"
-- CVV заборонено зберігати навіть тимчасово
```

💸 **-15 балів** + лекція про PCI DSS.

🧠 Зберігання повної картки = PCI DSS compliance = сотні тисяч доларів щорічних аудитів. Всі нормальні системи токенізують через платіжний шлюз.

---

## 5. Архітектурні помилки

### 5.1. One big table (OBT)

❌ Погано:
```sql
CREATE TABLE everything (
    id, user_name, user_email, user_phone,
    product_1_name, product_1_price,
    product_2_name, product_2_price,
    product_3_name, product_3_price,  -- жахливо
    order_date, ...
);
```

✅ Правильно: окремі таблиці users / products / orders / order_items.

💸 **-25 балів** — це порушення 1NF, повернення на переробку.

---

### 5.2. ENUM там, де треба довідник

❌ Погано:
```sql
category ENUM('Electronics', 'Books', 'Clothing', 'Food', 'Toys', 'Sports', 'Home', ...)
-- щоб додати категорію — ALTER TABLE → downtime
```

✅ Правильно:
```sql
CREATE TABLE categories (
    id INT PK AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    parent_id INT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

CREATE TABLE products (
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

💸 **-5 балів** — «як додати нову категорію без ALTER?».

🧠 ENUM — для фіксованих бізнес-правил (gender, order status). Для «списку який може рости» — таблиця.

---

### 5.3. Немає `created_at`/`updated_at`

❌ Погано:
```sql
CREATE TABLE posts (
    id, title, body
    -- і як ти відповіси «коли цей пост створено?»
);
```

✅ Правильно:
```sql
CREATE TABLE posts (
    id, title, body,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

💸 **-5 балів** — «як відсортувати пости за датою? як зрозуміти коли відредагували?».

---

### 5.4. Hard delete замість soft delete

❌ Погано:
```sql
DELETE FROM users WHERE id = 5;
-- історія замовлень втрачена, FK не спрацює (або CASCADE → замовлення видалились!)
```

✅ Правильно:
```sql
-- у схемі
deleted_at DATETIME NULL,

-- у коді
UPDATE users SET deleted_at = NOW() WHERE id = 5;
SELECT * FROM users WHERE deleted_at IS NULL;  -- активні
```

💸 **-5 балів** — «а якщо видалити юзера через рік, чи збережеться замовлення?».

🧠 Soft delete дозволяє зберегти історію. Фізично видаляти можна через cron-job після N років (GDPR right to be forgotten).

---

### 5.5. Snapshot ціни відсутній в order_items

❌ Погано:
```sql
CREATE TABLE order_items (
    order_id, product_id, quantity
    -- ціна береться з products.price → зміниться → історичне замовлення зламається
);
```

✅ Правильно:
```sql
CREATE TABLE order_items (
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL COMMENT 'snapshot price at time of order',
    total_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);
```

💸 **-5 балів** — «а якщо ціна зросте, чи перерахується історичне замовлення?».

🧠 Історичні записи = immutable. Якщо продукт подорожчав — нові замовлення з новою ціною, старі **не чіпаємо**.

---

## 6. Міграції та seed

### 6.1. Все в одному гігантському .sql файлі

❌ Погано:
```
database.sql  -- 2000 рядків, все разом: schema + data + sample records
```

✅ Правильно:
```
database/
├── migrations/
│   ├── 0001_create_users.sql
│   ├── 0002_create_categories.sql
│   ├── 0003_create_products.sql
│   └── ...
└── seeders/
    ├── roles_seeder.sql
    ├── categories_seeder.sql
    └── sample_data_seeder.sql
```

💸 **-3 бали** — «як додати нову таблицю в продакшн без втрати даних?».

🧠 Міграції = version control для схеми. Кожна зміна — окремий файл із timestamp/номером. У Laravel — `php artisan make:migration`.

---

### 6.2. Seed з production даними

❌ Погано:
```sql
-- seeder.sql
INSERT INTO users VALUES (1, 'Іван Петров', '+380501234567', 'ivan@real-email.com', ...);
-- справжня людина, справжній email — порушення GDPR
```

✅ Правильно:
```sql
INSERT INTO users VALUES (1, 'Test User', '+380000000001', 'test@example.com', ...);
-- fake data з бібліотеки типу Faker
```

💸 **-5 балів** — GDPR.

---

## 7. Запити (для LR5+)

### 7.1. N+1 проблема

❌ Погано:
```php
$orders = $db->query("SELECT * FROM orders");
foreach ($orders as $order) {
    $user = $db->query("SELECT * FROM users WHERE id = {$order['user_id']}");
    // 100 замовлень = 101 запит
}
```

✅ Правильно:
```php
$orders = $db->query("
    SELECT o.*, u.email, u.first_name
    FROM orders o
    JOIN users u ON u.id = o.user_id
");
// 1 запит
```

💸 **-5 балів** — демонстрація EXPLAIN в профайлері.

---

### 7.2. SELECT *

❌ Погано:
```sql
SELECT * FROM users WHERE email = 'x@y.com';
-- тягне password_hash, phone, адреси, усе що не треба
```

✅ Правильно:
```sql
SELECT id, email, first_name, last_name FROM users WHERE email = 'x@y.com';
```

💸 **-2 бали** — особливо якщо password_hash потрапляє в API response.

---

### 7.3. SQL Injection

❌ Погано:
```php
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
// "' OR '1'='1" → SELECT всіх користувачів
```

✅ Правильно:
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $_POST['email']]);
```

💸 **-30 балів** (!!!) — це критична вразливість, automatic -2 на іспиті.

---

## Топ-5 найгірших втрат балів

| # | Помилка | Втрата |
|---|---------|--------|
| 1 | SQL Injection (конкатенація рядків) | -30 |
| 2 | One Big Table (порушення 1NF) | -25 |
| 3 | Password plaintext/MD5 | -15 |
| 4 | Збереження повної картки + CVV | -15 |
| 5 | Немає FK (orphaned records можливі) | -10 |

## Топ-10 «дрібних» помилок (сумарно можуть знизити на 2 бали)

| # | Помилка | Втрата |
|---|---------|--------|
| 1 | VARCHAR(255) всюди | -3 |
| 2 | FLOAT/DOUBLE для грошей | -5 |
| 3 | VARCHAR для boolean | -2 |
| 4 | DATETIME коли треба TIMESTAMP | -2 |
| 5 | utf8 замість utf8mb4 | -3 |
| 6 | Немає ON DELETE | -3 |
| 7 | Junction з сурогатним id | -5 |
| 8 | Немає composite UNIQUE у booking | -10 |
| 9 | Відсутність індексів на FK | -5 |
| 10 | Немає `created_at`/`updated_at` | -5 |

## Посилання

- Найкращі практики: [schemas.md § 3](schemas.md#3-best-practices-для-всіх-схем)
- Чекліст перед здачею: [checklist-lr4.md](checklist-lr4.md)
- Запити (для демонстрації): [example-queries.md](example-queries.md)
