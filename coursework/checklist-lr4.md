# Чекліст самоперевірки ЛР4 (проєктування БД)

> **Призначення:** перевірка схеми БД перед здачею / захистом.
> Пройдіть ВСІ пункти — кожен невиконаний = мінус бали.
> **Орієнтир:** для оцінки 5 — всі пункти мають бути ✅ (окрім «опціонально»).

## Як використовувати

1. Відкрийте свій DDL (`.sql` файл або міграції).
2. Проходьте по розділах згори вниз.
3. Для кожного пункту — або ✅ (так), або 🔧 (треба виправити).
4. Всі 🔧 → виправте → переконайтеся що ✅.

---

## Розділ 1. Базова валідність схеми

### 1.1. Нормалізація (3NF)

- [ ] Немає повторюваних груп полів (напр. `phone1/phone2/phone3` → окрема таблиця `phones`)
- [ ] Немає часткової залежності від частини складеного ключа (2NF)
- [ ] Немає транзитивних залежностей (3NF) — всі non-key поля залежать тільки від PK
- [ ] ENUM/довідники винесено в окремі таблиці, якщо значення можуть змінюватися (статуси замовлень — ок залишити ENUM, ролі/категорії — виносити в таблиці)
- [ ] **Опціонально (оцінка 5):** BCNF для критичних таблиць

### 1.2. Первинні ключі (PK)

- [ ] Кожна таблиця має PK
- [ ] PK = `INT AUTO_INCREMENT` або `BIGINT AUTO_INCREMENT` (не VARCHAR для внутрішніх ID)
- [ ] Junction tables (N↔N) мають composite PK `(entity_a_id, entity_b_id)` — **не** окремий `id` як сурогат
- [ ] UUID використовується тільки для external-facing ID (напр. `booking_code`, `order_number`) — не як PK

### 1.3. Зовнішні ключі (FK)

- [ ] **Кожне** поле з суфіксом `_id` має `FOREIGN KEY` constraint
- [ ] Кожен FK має явно вказаний `ON DELETE` (CASCADE / RESTRICT / SET NULL / NO ACTION)
- [ ] Правило `ON DELETE` логічне для домену:
  - Користувач видаляється → замовлення залишаються (RESTRICT або SET NULL для `user_id`)
  - Замовлення видаляється → `order_items` видаляються (CASCADE)
  - Категорія видаляється → товари не видаляються (SET NULL або RESTRICT)
- [ ] **Опціонально:** `ON UPDATE CASCADE` вказано явно

---

## Розділ 2. Типи даних

### 2.1. Правильні типи

- [ ] Рядки: `VARCHAR(N)` з адекватним N (email=255, title=200, phone=20), **не** `VARCHAR(255)` скрізь за замовчуванням
- [ ] Довгий текст: `TEXT` (description, comment, notes), `MEDIUMTEXT` для статей
- [ ] Числа: `INT` для кількостей, `BIGINT` для великих лічильників, `DECIMAL(10,2)` для грошей (**НЕ** `FLOAT`/`DOUBLE` для грошей)
- [ ] Дати: `DATE` для днів народжень, `DATETIME` для подій, `TIMESTAMP` для системних (created_at/updated_at)
- [ ] Boolean: `BOOLEAN` або `TINYINT(1)` (не `VARCHAR('true'/'false')`)
- [ ] JSON: використовуйте `JSON` тип (MySQL 5.7+) для гнучких структур, **не** для звичайних полів

### 2.2. Кодування

- [ ] Таблиці з Unicode-контентом: `DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`
- [ ] Не використовується застарілий `utf8` (це 3-byte, ламає emoji та деякі ієрогліфи)

### 2.3. ENUM vs лookup

- [ ] Невеликий фіксований список (статуси, гендер) → ENUM
- [ ] Великий список що може змінюватися (категорії, теги) → окрема таблиця
- [ ] ENUM значення — lowercase snake_case (`'pending'`, `'in_progress'`, не `'In Progress'`)

---

## Розділ 3. Обмеження (Constraints)

### 3.1. UNIQUE

- [ ] `email` у users — UNIQUE
- [ ] `username`/`slug` (якщо є) — UNIQUE
- [ ] `order_number`/`invoice_number`/`booking_code` — UNIQUE (external identifiers)
- [ ] Business keys (ISBN, SKU, insurance_number) — UNIQUE
- [ ] Junction з додатковим сенсом (напр. `UNIQUE(session_id, seat_id)` у tickets) — для запобігання race condition
- [ ] Composite UNIQUE для бізнес-правил («одна людина — одна реакція на пост»): `UNIQUE(user_id, post_id)` у likes

### 3.2. CHECK

- [ ] `CHECK (price >= 0)` на грошових полях
- [ ] `CHECK (quantity > 0)` на кількості в order_items
- [ ] `CHECK (rating BETWEEN 1 AND 5)` на рейтингах
- [ ] `CHECK (birth_date < CURRENT_DATE)` на даті народження
- [ ] `CHECK (end_date > start_date)` на періодах
- [ ] **Опціонально (оцінка 5):** `CHECK (follower_id != followee_id)` у follows (не можна підписатися на себе)

### 3.3. NOT NULL

- [ ] Обов'язкові поля мають `NOT NULL` (email, password_hash, title у товарів)
- [ ] FK поля здебільшого NOT NULL (окрім опціональних зв'язків типу `coupon_id`, `parent_id`)
- [ ] `created_at`/`updated_at` — NOT NULL з DEFAULT

---

## Розділ 4. Індекси

### 4.1. Обов'язкові індекси

- [ ] **Кожне** FK поле має окремий індекс (MySQL створює його автоматично для FK, але перевірте)
- [ ] Поля для частих WHERE: `status`, `is_active`, `category_id`, `user_id`
- [ ] Поля для ORDER BY: `created_at`, `published_at`
- [ ] Поля для пошуку: `email`, `phone`, `order_number`

### 4.2. Composite індекси

- [ ] Індекс на (`category_id`, `is_active`, `created_at`) — для «активні товари в категорії, останні першими»
- [ ] Індекс на (`user_id`, `status`) — для «мої замовлення зі статусом X»
- [ ] Порядок полів у composite index: від найвибірковішого до найменш вибіркового

### 4.3. FULLTEXT (якщо є пошук)

- [ ] `FULLTEXT INDEX` на полях для пошуку в контенті (title, description)
- [ ] **Опціонально:** окремий механізм пошуку (Elasticsearch) — для великих об'ємів

### 4.4. Анти-шаблони

- [ ] Немає індексів на `is_deleted`/`deleted_at` окремо — воно в composite з іншими
- [ ] Немає дублюючих індексів (один індекс на `(a, b)` покриває і `(a)` — окремий не потрібен)

---

## Розділ 5. Timestamps + audit

### 5.1. Базові timestamps

- [ ] Кожна основна таблиця має `created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`
- [ ] Змінювані таблиці мають `updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`
- [ ] Довідникові таблиці (ролі, категорії) — можна без updated_at

### 5.2. Soft delete

- [ ] Таблиці з історичним зв'язком (users, products, posts) мають `deleted_at DATETIME NULL`
- [ ] **НЕ** використовується `is_deleted BOOLEAN` замість `deleted_at` — втрачається інформація коли видалили
- [ ] SELECT запити фільтрують `WHERE deleted_at IS NULL` (в моделях/repositories)

### 5.3. Audit log

- [ ] **Для оцінки 5:** є таблиця `audit_log` з полями: `user_id`, `action`, `entity_type`, `entity_id`, `old_values JSON`, `new_values JSON`, `created_at`, `ip_address`
- [ ] Критичні зміни (видалення, зміна ролей, фінансові операції) логуються через тригер або сервіс

---

## Розділ 6. Безпека

### 6.1. Паролі

- [ ] Поле називається `password_hash` (не `password`)
- [ ] Тип `VARCHAR(255)` (bcrypt = 60 символів, але залишаємо запас для майбутніх алгоритмів)
- [ ] Коментар: `COMMENT 'bcrypt/argon2, NEVER plaintext'`
- [ ] **НЕ** зберігаються: plaintext password, reversible encrypted password, SHA1/MD5 hash

### 6.2. Токени

- [ ] Таблиця `password_resets` з `token VARCHAR(255) UNIQUE`, `expires_at DATETIME`, `used_at DATETIME NULL`
- [ ] Таблиця `sessions` або `remember_tokens` з подібною структурою
- [ ] Токени — довгі random strings (64+ символів), не передбачувані

### 6.3. PII (Personal Identifiable Information)

- [ ] Чутливі поля (паспорт, ІПН, номер картки) — **не зберігаються** або шифруються (AES-256)
- [ ] Якщо зберігаєте повну адресу — виноситься в окрему таблицю `addresses` з FK до user

### 6.4. Login attempts

- [ ] **Опціонально (оцінка 5):** таблиця `login_attempts` для rate limiting + blocked IP detection

---

## Розділ 7. RBAC (Role-Based Access Control)

### 7.1. Мінімум для «простих» варіантів

- [ ] Поле `role` ENUM у users АБО окрема таблиця `roles` з FK `user.role_id`

### 7.2. Для оцінки 5

- [ ] Таблиці `roles`, `permissions`, `user_roles` (N↔N), `role_permissions` (N↔N)
- [ ] Seed: мінімум 3 ролі — `customer`/`user`, `admin`, `manager`/`moderator`
- [ ] Seed: permissions описують дії — `user.create`, `product.update`, `order.view_all`
- [ ] У коді є middleware/guard що перевіряє permission перед action

---

## Розділ 8. Документація схеми

### 8.1. ER-діаграма

- [ ] **Фізична ER-діаграма** (таблиці, поля, типи, зв'язки) — Рис. 2.X у курсовій
- [ ] **Логічна ER-діаграма** (сутності, зв'язки — без технічних деталей) — для розділу «аналіз»
- [ ] Діаграма читається: нормальний шрифт, зв'язки не перетинаються, кардинальність позначена (1:N, N:M)

### 8.2. DDL

- [ ] Файл `database.sql` або папка `migrations/` з окремими файлами
- [ ] CREATE TABLE у порядку залежностей (спочатку без FK, потім з FK)
- [ ] Кожна таблиця має `ENGINE=InnoDB` (не MyISAM)
- [ ] Коментарі для нестандартних полів

### 8.3. Словник даних (опціонально для оцінки 5)

- [ ] Таблиця з описом кожного поля: Назва / Тип / NULL / Default / Опис / Приклад

---

## Розділ 9. Тести на схему

### 9.1. Чи можна вставити базові дані?

- [ ] `INSERT` у всі таблиці в правильному порядку проходить без помилок
- [ ] Обов'язкові поля не допускають NULL (помилка при INSERT без них)
- [ ] UNIQUE спрацьовує (помилка при INSERT дубля email)
- [ ] CHECK спрацьовує (помилка при INSERT `price = -100`)
- [ ] FK спрацьовує (помилка при INSERT `order.user_id = 9999` якщо такого user немає)

### 9.2. Чи правильно працюють CASCADE?

- [ ] DELETE users → orders залишаються (user_id = NULL або RESTRICT)
- [ ] DELETE orders → order_items видаляються
- [ ] UPDATE categories.id → books.category_id оновлюється (якщо ON UPDATE CASCADE)

### 9.3. Чи можна написати seed?

- [ ] Є seed-файл з тестовими даними (мінімум: 3 users, 2 categories, 5 products, 2 orders)
- [ ] Seed виконується без помилок на пустій базі

---

## Розділ 10. Специфічні для категорії вимоги

### 10.1. E-commerce (v1-v4, v8, v11, v13, v21, v23, v25, v27)

- [ ] Є `cart_items` — окремо від `order_items` (cart = поточний кошик, order = збережене замовлення)
- [ ] `order_items.unit_price` — **snapshot** ціни на момент замовлення (щоб зміна ціни товару не вплинула на історичне замовлення)
- [ ] Є таблиця payments (окремо від orders — один order може мати кілька payment attempts)

### 10.2. Booking (v3, v14, v16, v18, v19, v24)

- [ ] `UNIQUE(slot_entity_id, slot_time_id)` — запобігає подвійному бронюванню
- [ ] Є ENUM `status`: pending/confirmed/cancelled/completed
- [ ] Є поле `cancellation_reason TEXT NULL` — для історії відмов

### 10.3. Catalog (v5, v6, v7, v26)

- [ ] Є розділення `item` (логічна одиниця — книга) і `copy`/`instance` (фізичний примірник — книга з inventory №)
- [ ] `copies.status`: available/loaned/damaged/lost
- [ ] Історія використання (loans, history) — окрема таблиця з датами borrowed/returned

### 10.4. Service-order (v2, v10, v12, v15, v17, v20, v22, v28, v29)

- [ ] Є `service_status_log` або `order_status_log` — історія змін статусу
- [ ] Тригер AFTER UPDATE на orders → INSERT в log (показати на захисті!)
- [ ] Є поле `assigned_to` (виконавець) або окрема таблиця `assignments`

### 10.5. UGC (v9, v30)

- [ ] Comments мають `parent_id` для nested
- [ ] Є `likes`/`reactions` з composite UNIQUE `(user_id, entity_id)`
- [ ] Є `follows`/`subscriptions` з `CHECK (follower_id != followee_id)`
- [ ] Content має `status`: draft/published/archived/deleted
- [ ] Content має `slug UNIQUE` для SEO-friendly URLs

---

## Фінальний чек перед здачею

- [ ] Всі пункти розділів 1-9 ✅
- [ ] Пункти розділу 10 для **вашої** категорії ✅
- [ ] ER-діаграма експортована у PNG і вставлена в курсову
- [ ] DDL файл окремо, коментований
- [ ] Seed з тестовими даними запускається
- [ ] Можу пояснити **кожне** рішення (чому ON DELETE CASCADE, чому ENUM а не таблиця, чому композитний index)
- [ ] Немає закоментованого/мертвого коду в DDL
- [ ] Файл пройшов `mysql --syntax-check` (або еквівалент у MariaDB)

## Калькулятор балів

| Виконано | Оцінка |
|----------|--------|
| Розділи 1-4 повністю | 60-74 |
| + розділ 5 (timestamps + soft delete) | 75-84 |
| + розділи 6-7 (security + RBAC базовий) | 85-89 |
| + розділ 8 повністю + розділ 10 для категорії + пункти «для оцінки 5» | 90-100 |

## Посилання

- Повні схеми: [schemas.md](schemas.md)
- ER-діаграми: [er-diagrams.md](er-diagrams.md)
- Типові помилки (чого уникати): [typical-mistakes.md](typical-mistakes.md)
- Приклади запитів: [example-queries.md](example-queries.md)
