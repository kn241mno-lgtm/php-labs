# Приклади SQL-запитів для демонстрації схем

> **Призначення:** топ запитів на кожну категорію — для демонстрації схеми на захисті курсової та в LR5-LR7.
> Показує **як саме** бізнес-логіка реалізовується через SQL.
> Студент має вміти пояснити кожен запит: що він робить + чому саме так.

## Зміст

1. [E-commerce (v1 Книгарня)](#1-e-commerce-v1-книгарня)
2. [Booking (v3 Кінотеатр)](#2-booking-v3-кінотеатр)
3. [Catalog (v7 Бібліотека)](#3-catalog-v7-бібліотека)
4. [Service-order (v20 Пральня)](#4-service-order-v20-пральня)
5. [UGC (v30 Кулінарний блог)](#5-ugc-v30-кулінарний-блог)
6. [Загальні запити (RBAC, audit)](#6-загальні-запити-rbac-audit)

---

## 1. E-commerce (v1 Книгарня)

### 1.1. Каталог книг з категорією + автором + фото (JOIN)

```sql
SELECT
    b.id, b.title, b.price, b.stock,
    c.name AS category,
    p.name AS publisher,
    GROUP_CONCAT(a.full_name SEPARATOR ', ') AS authors,
    bi.image_url AS cover
FROM books b
JOIN categories c ON c.id = b.category_id
LEFT JOIN publishers p ON p.id = b.publisher_id
LEFT JOIN book_authors ba ON ba.book_id = b.id
LEFT JOIN authors a ON a.id = ba.author_id
LEFT JOIN book_images bi ON bi.book_id = b.id AND bi.is_primary = TRUE
WHERE b.deleted_at IS NULL
  AND b.stock > 0
GROUP BY b.id, c.name, p.name, bi.image_url
ORDER BY b.created_at DESC
LIMIT 20;
```

**Що демонструє:**
- LEFT JOIN для опціональних зв'язків (publisher може бути NULL)
- GROUP_CONCAT для N↔N (book ↔ authors)
- Композитне фільтрування (soft delete + stock > 0)
- Пагінація через LIMIT

---

### 1.2. Повне замовлення з позиціями + сумою (Transaction-safe READ)

```sql
-- Деталі замовлення
SELECT
    o.id, o.order_number, o.current_status, o.created_at,
    u.email, u.first_name, u.last_name,
    a.street, a.city, a.postal_code,
    COALESCE(c.code, '—') AS coupon_code,
    COALESCE(c.discount_percent, 0) AS discount_percent,
    o.total_amount
FROM orders o
JOIN users u ON u.id = o.user_id
JOIN addresses a ON a.id = o.address_id
LEFT JOIN coupons c ON c.id = o.coupon_id
WHERE o.id = :order_id;

-- Позиції замовлення
SELECT
    oi.id, oi.quantity, oi.unit_price,
    oi.quantity * oi.unit_price AS subtotal,
    b.title, b.isbn
FROM order_items oi
JOIN books b ON b.id = oi.book_id
WHERE oi.order_id = :order_id
ORDER BY oi.id;
```

**Що демонструє:**
- Розділення на 2 запити — щоб головна інформація не дублювалася для кожної позиції
- COALESCE для NULL-safe відображення опціональних полів
- Обчислюване поле (subtotal) на льоту без збереження

---

### 1.3. Кошик → замовлення (Transaction)

```sql
START TRANSACTION;

-- 1. Створити замовлення
INSERT INTO orders (user_id, address_id, coupon_id, order_number, current_status, total_amount)
VALUES (:user_id, :address_id, :coupon_id, :order_number, 'pending', 0);
SET @order_id = LAST_INSERT_ID();

-- 2. Перенести позиції з кошика (snapshot ціни!)
INSERT INTO order_items (order_id, book_id, quantity, unit_price)
SELECT @order_id, ci.book_id, ci.quantity, b.price
FROM cart_items ci
JOIN books b ON b.id = ci.book_id
WHERE ci.user_id = :user_id;

-- 3. Списати залишки
UPDATE books b
JOIN cart_items ci ON ci.book_id = b.id
SET b.stock = b.stock - ci.quantity
WHERE ci.user_id = :user_id;

-- 4. Перерахувати суму замовлення
UPDATE orders
SET total_amount = (
    SELECT SUM(oi.quantity * oi.unit_price)
    FROM order_items oi
    WHERE oi.order_id = @order_id
)
WHERE id = @order_id;

-- 5. Очистити кошик
DELETE FROM cart_items WHERE user_id = :user_id;

COMMIT;
-- ROLLBACK у випадку помилки
```

**Що демонструє:**
- Transaction — або всі 5 запитів, або жоден
- Snapshot ціни (`b.price` в момент INSERT INTO order_items)
- LAST_INSERT_ID() + session variable для передачі ID
- CORRELATED UPDATE (оновлення stock за даними з cart_items)

---

### 1.4. Топ-10 книг за продажами (Aggregation)

```sql
SELECT
    b.id, b.title, b.price,
    SUM(oi.quantity) AS total_sold,
    SUM(oi.quantity * oi.unit_price) AS revenue,
    COUNT(DISTINCT o.user_id) AS unique_buyers
FROM books b
JOIN order_items oi ON oi.book_id = b.id
JOIN orders o ON o.id = oi.order_id
WHERE o.current_status IN ('paid', 'delivered')
  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY b.id, b.title, b.price
HAVING total_sold > 0
ORDER BY total_sold DESC
LIMIT 10;
```

**Що демонструє:**
- Агрегатні функції (SUM, COUNT DISTINCT)
- HAVING для фільтрації агрегатів
- WHERE ... IN (...) для множинних статусів
- DATE_SUB для темпоральних обмежень

---

### 1.5. Середній рейтинг книги

```sql
SELECT
    b.id, b.title,
    COUNT(r.id) AS review_count,
    ROUND(AVG(r.rating), 2) AS avg_rating
FROM books b
LEFT JOIN reviews r ON r.book_id = b.id
WHERE b.deleted_at IS NULL
GROUP BY b.id, b.title
ORDER BY avg_rating DESC, review_count DESC
LIMIT 20;
```

**Що демонструє:**
- LEFT JOIN — книги без відгуків теж у вибірці (avg_rating = NULL)
- ROUND для красивого відображення
- Tie-breaking через вторинне ORDER BY (спочатку рейтинг, потім кількість)

---

## 2. Booking (v3 Кінотеатр)

### 2.1. Активні сеанси на сьогодні + статистика вільних місць

```sql
SELECT
    s.id, s.start_at, s.price,
    m.title AS movie, m.duration_minutes,
    h.name AS hall,
    h.total_capacity,
    COUNT(t.id) AS tickets_sold,
    h.total_capacity - COUNT(t.id) AS seats_available,
    ROUND(100.0 * COUNT(t.id) / h.total_capacity, 1) AS fill_percent
FROM sessions s
JOIN movies m ON m.id = s.movie_id
JOIN halls h ON h.id = s.hall_id
LEFT JOIN tickets t ON t.session_id = s.id AND t.status IN ('reserved', 'paid', 'used')
WHERE s.status = 'scheduled'
  AND DATE(s.start_at) = CURDATE()
GROUP BY s.id, s.start_at, s.price, m.title, m.duration_minutes, h.name, h.total_capacity
ORDER BY s.start_at;
```

**Що демонструє:**
- Обчислення залишку місць (capacity - sold)
- Фільтрація LEFT JOIN через умову в `ON` (не в WHERE — інакше зіпсує LEFT JOIN)
- Обчислення відсотка заповнення

---

### 2.2. Купівля квитка з захистом від race condition

```sql
START TRANSACTION;

-- 1. Перевірка доступності (SELECT ... FOR UPDATE блокує рядок)
SELECT id FROM seats
WHERE id = :seat_id
  AND id NOT IN (
      SELECT seat_id FROM tickets
      WHERE session_id = :session_id AND status IN ('reserved', 'paid')
  )
FOR UPDATE;

-- 2. Якщо доступно → створити квиток
-- UNIQUE(session_id, seat_id) гарантує що паралельний запит провалиться з помилкою
INSERT INTO tickets (session_id, seat_id, user_id, price, status, booking_code)
VALUES (:session_id, :seat_id, :user_id, :price, 'reserved', :booking_code);

-- 3. Позначити користувачеві історично
-- (користувач вже в tickets, окремо не треба)

COMMIT;
-- Якщо INSERT провалиться через UNIQUE violation → ROLLBACK
```

**Що демонструє:**
- Комбінація application-level check + database-level UNIQUE constraint
- SELECT ... FOR UPDATE — pessimistic locking
- Transaction для атомарності

**Важливо пояснити на захисті:**
- Перший рівень — application перевіряє «чи вільне місце».
- Другий рівень — UNIQUE KEY (session_id, seat_id) ловить race condition якщо два users одночасно пройшли перевірку.
- Обов'язково **обидва рівні** — перший для UX (дати помилку одразу), другий для гарантії.

---

### 2.3. Схема залу з позначкою вільних/зайнятих місць

```sql
SELECT
    s.id AS seat_id,
    s.row_number,
    s.seat_number,
    s.seat_type,
    s.price_multiplier,
    CASE
        WHEN t.id IS NULL THEN 'available'
        WHEN t.status = 'reserved' THEN 'held'
        WHEN t.status = 'paid' THEN 'taken'
        WHEN t.status = 'cancelled' THEN 'available'
        ELSE 'unknown'
    END AS display_status
FROM seats s
LEFT JOIN tickets t ON t.seat_id = s.id AND t.session_id = :session_id
WHERE s.hall_id = (SELECT hall_id FROM sessions WHERE id = :session_id)
ORDER BY s.row_number, s.seat_number;
```

**Що демонструє:**
- CASE WHEN для перетворення внутрішнього статусу в display-friendly
- LEFT JOIN для «всі місця, деякі мають квитки»
- Підзапит у WHERE для hall_id

---

### 2.4. Звіт — найпопулярніші фільми за тиждень

```sql
SELECT
    m.id, m.title,
    COUNT(DISTINCT s.id) AS sessions_count,
    COUNT(t.id) AS tickets_sold,
    SUM(t.price) AS revenue,
    ROUND(AVG(t.price), 2) AS avg_ticket_price
FROM movies m
JOIN sessions s ON s.movie_id = m.id
JOIN tickets t ON t.session_id = s.id
WHERE t.status IN ('paid', 'used')
  AND s.start_at BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()
GROUP BY m.id, m.title
ORDER BY tickets_sold DESC
LIMIT 10;
```

**Що демонструє:**
- Множинні JOIN з агрегацією
- BETWEEN для date range
- DISTINCT у COUNT для унікальних сеансів

---

## 3. Catalog (v7 Бібліотека)

### 3.1. Пошук книги + статус доступних примірників

```sql
SELECT
    b.id, b.title, b.isbn,
    COUNT(CASE WHEN jc.status = 'available' THEN 1 END) AS available_copies,
    COUNT(CASE WHEN jc.status = 'loaned' THEN 1 END) AS loaned_copies,
    COUNT(jc.id) AS total_copies,
    GROUP_CONCAT(DISTINCT a.full_name ORDER BY ba.author_order SEPARATOR ', ') AS authors
FROM books b
LEFT JOIN journal_copies jc ON jc.book_id = b.id
LEFT JOIN book_authors ba ON ba.book_id = b.id
LEFT JOIN authors a ON a.id = ba.author_id
WHERE MATCH(b.title, b.description) AGAINST (:search_query IN NATURAL LANGUAGE MODE)
GROUP BY b.id, b.title, b.isbn
HAVING available_copies > 0 OR total_copies = 0
ORDER BY available_copies DESC;
```

**Що демонструє:**
- FULLTEXT пошук (`MATCH ... AGAINST`)
- Conditional COUNT (`COUNT(CASE WHEN ...)`)
- GROUP_CONCAT з сортуванням всередині

---

### 3.2. Видача книги читачеві (lifecycle update)

```sql
START TRANSACTION;

-- 1. Перевірка ліміту (максимум 5 активних позичок)
SELECT COUNT(*) INTO @active_loans
FROM loans
WHERE user_id = :user_id AND returned_at IS NULL;

-- Application перевіряє @active_loans < 5 → якщо ні, ROLLBACK

-- 2. Створити позичку
INSERT INTO loans (copy_id, user_id, borrowed_at, due_at, status)
VALUES (:copy_id, :user_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'active');

-- 3. Оновити статус примірника
UPDATE journal_copies
SET status = 'loaned'
WHERE id = :copy_id;

COMMIT;
```

**Що демонструє:**
- Transaction для 2-фазної операції (створити позичку + оновити статус)
- DATE_ADD для обчислення due_at (14 днів позички)
- Business rule enforcement (max 5 активних)

---

### 3.3. Прострочені позички + нарахування штрафу

```sql
SELECT
    l.id, l.borrowed_at, l.due_at,
    DATEDIFF(CURDATE(), l.due_at) AS days_overdue,
    DATEDIFF(CURDATE(), l.due_at) * 5 AS fine_uah,
    u.email, u.first_name, u.last_name,
    b.title, b.isbn
FROM loans l
JOIN users u ON u.id = l.user_id
JOIN journal_copies jc ON jc.id = l.copy_id
JOIN books b ON b.id = jc.book_id
WHERE l.returned_at IS NULL
  AND l.due_at < CURDATE()
ORDER BY days_overdue DESC;
```

**Що демонструє:**
- DATEDIFF для обчислення днів прострочення
- Ланцюг JOIN (loan → copy → book)
- Filter на NULL (`returned_at IS NULL` = активні позички)

---

### 3.4. Черга резервацій для книги

```sql
SELECT
    r.id, r.reserved_at, r.queue_position,
    u.email, u.first_name, u.last_name,
    CASE
        WHEN r.queue_position = 1 THEN 'Наступний'
        WHEN r.queue_position <= 3 THEN 'Скоро'
        ELSE CONCAT('Позиція ', r.queue_position)
    END AS status_display
FROM reservations r
JOIN users u ON u.id = r.user_id
WHERE r.book_id = :book_id
  AND r.status = 'waiting'
ORDER BY r.queue_position;
```

**Що демонструє:**
- Conditional display через CASE
- Сортування за полем що визначає порядок

---

## 4. Service-order (v20 Пральня)

### 4.1. Повна історія замовлення (з status log)

```sql
-- Основна інформація
SELECT
    o.id, o.order_number, o.current_status, o.total_amount,
    o.accepted_at, o.ready_at, o.delivered_at,
    u.email, u.first_name, u.last_name
FROM orders o
JOIN users u ON u.id = o.user_id
WHERE o.id = :order_id;

-- Позиції замовлення
SELECT
    oi.id, oi.quantity, oi.unit_price, oi.total_price,
    s.name AS service, s.unit_type, sc.name AS category
FROM order_items oi
JOIN services s ON s.id = oi.service_id
JOIN service_categories sc ON sc.id = s.category_id
WHERE oi.order_id = :order_id;

-- Історія статусів (з тригера)
SELECT
    osl.id, osl.old_status, osl.new_status, osl.changed_at, osl.comment,
    u.first_name AS changed_by_name
FROM order_status_log osl
LEFT JOIN users u ON u.id = osl.changed_by_user_id
WHERE osl.order_id = :order_id
ORDER BY osl.changed_at;
```

**Що демонструє:**
- 3 окремих запити для structured data
- Використання тригерного аудиту

---

### 4.2. Тригер для автоматичного логування (MySQL)

```sql
DELIMITER $$

CREATE TRIGGER log_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.current_status != NEW.current_status THEN
        INSERT INTO order_status_log (
            order_id,
            changed_by_user_id,
            old_status,
            new_status,
            changed_at
        ) VALUES (
            NEW.id,
            @app_user_id,  -- set in application before UPDATE
            OLD.current_status,
            NEW.current_status,
            NOW()
        );
    END IF;
END$$

DELIMITER ;
```

**У коді PHP перед UPDATE:**
```sql
SET @app_user_id = :user_id_from_session;
UPDATE orders SET current_status = 'ready' WHERE id = :order_id;
```

**Що демонструє:**
- AFTER UPDATE тригер
- Session variable для передачі контексту юзера
- Умовний INSERT тільки при реальній зміні статусу

---

### 4.3. SLA-репорт: середній час виконання

```sql
SELECT
    sc.name AS category,
    COUNT(o.id) AS orders_count,
    ROUND(AVG(TIMESTAMPDIFF(HOUR, o.accepted_at, o.ready_at)), 1) AS avg_hours_to_ready,
    ROUND(AVG(TIMESTAMPDIFF(HOUR, o.ready_at, o.delivered_at)), 1) AS avg_hours_to_deliver,
    ROUND(AVG(TIMESTAMPDIFF(HOUR, o.accepted_at, o.delivered_at)), 1) AS avg_total_hours
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN services s ON s.id = oi.service_id
JOIN service_categories sc ON sc.id = s.category_id
WHERE o.delivered_at IS NOT NULL
  AND o.accepted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY sc.id, sc.name
ORDER BY avg_total_hours DESC;
```

**Що демонструє:**
- TIMESTAMPDIFF для обчислення тривалості
- Агрегація за категорією
- Виключення незавершених замовлень (`delivered_at IS NOT NULL`)

---

## 5. UGC (v30 Кулінарний блог)

### 5.1. Рецепти з інгредієнтами + кроками (hierarchical)

```sql
-- Основна інформація
SELECT
    r.id, r.title, r.slug, r.description, r.cooking_instructions,
    r.prep_minutes, r.cook_minutes, r.servings, r.difficulty,
    r.cover_image_url, r.published_at,
    u.email AS author_email, u.first_name AS author_name,
    c.name AS category,
    (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) AS likes_count,
    (SELECT COUNT(*) FROM comments WHERE recipe_id = r.id AND deleted_at IS NULL) AS comments_count
FROM recipes r
JOIN users u ON u.id = r.user_id
LEFT JOIN categories c ON c.id = r.category_id
WHERE r.slug = :slug
  AND r.status = 'published'
  AND r.deleted_at IS NULL;

-- Інгредієнти
SELECT
    ri.id, ri.quantity, ri.unit, ri.notes,
    i.name AS ingredient_name
FROM recipe_ingredients ri
JOIN ingredients i ON i.id = ri.ingredient_id
WHERE ri.recipe_id = :recipe_id
ORDER BY ri.id;

-- Кроки
SELECT
    step_order, description, image_url, duration_seconds
FROM recipe_steps
WHERE recipe_id = :recipe_id
ORDER BY step_order;

-- Теги
SELECT t.name
FROM tags t
JOIN recipe_tags rt ON rt.tag_id = t.id
WHERE rt.recipe_id = :recipe_id;
```

**Що демонструє:**
- Correlated subqueries для counters (likes, comments)
- Розділення на 4 запити для structured data
- Soft filter на deleted_at

---

### 5.2. Nested коментарі (recursive CTE)

```sql
WITH RECURSIVE comment_tree AS (
    -- Anchor: top-level comments
    SELECT
        c.id, c.parent_id, c.user_id, c.body, c.created_at,
        1 AS depth,
        CAST(LPAD(c.id, 10, '0') AS CHAR(500)) AS path
    FROM comments c
    WHERE c.recipe_id = :recipe_id
      AND c.parent_id IS NULL
      AND c.deleted_at IS NULL

    UNION ALL

    -- Recursive: replies
    SELECT
        c.id, c.parent_id, c.user_id, c.body, c.created_at,
        ct.depth + 1,
        CONCAT(ct.path, '/', LPAD(c.id, 10, '0'))
    FROM comments c
    JOIN comment_tree ct ON ct.id = c.parent_id
    WHERE c.deleted_at IS NULL
)
SELECT
    ct.id, ct.body, ct.depth, ct.created_at,
    u.email, u.first_name
FROM comment_tree ct
JOIN users u ON u.id = ct.user_id
ORDER BY ct.path;
```

**Що демонструє:**
- Recursive CTE (MySQL 8+) для ієрархічних структур
- LPAD для правильного сортування (01/02/10 замість 01/02/1)
- Глибина вкладеності для UI (відступи)

---

### 5.3. Стрічка від підписок (fan-out read)

```sql
SELECT
    r.id, r.title, r.slug, r.cover_image_url, r.published_at,
    u.email AS author_email, u.first_name AS author_name,
    EXISTS (
        SELECT 1 FROM likes l
        WHERE l.recipe_id = r.id AND l.user_id = :current_user_id
    ) AS is_liked_by_me,
    EXISTS (
        SELECT 1 FROM bookmarks b
        WHERE b.recipe_id = r.id AND b.user_id = :current_user_id
    ) AS is_bookmarked_by_me
FROM recipes r
JOIN users u ON u.id = r.user_id
JOIN follows f ON f.followee_id = r.user_id AND f.follower_id = :current_user_id
WHERE r.status = 'published'
  AND r.deleted_at IS NULL
  AND r.published_at <= NOW()
ORDER BY r.published_at DESC
LIMIT 20 OFFSET :page_offset;
```

**Що демонструє:**
- EXISTS для перевірки (is_liked, is_bookmarked)
- JOIN на follows для фільтрації «тільки від тих на кого підписаний»
- Пагінація OFFSET/LIMIT

---

### 5.4. Топ-авторів тижня (aggregation + tie-breaker)

```sql
SELECT
    u.id, u.email, u.first_name, u.last_name,
    COUNT(DISTINCT r.id) AS recipes_published,
    SUM(likes_agg.likes_count) AS total_likes,
    SUM(bookmarks_agg.bookmarks_count) AS total_bookmarks
FROM users u
JOIN recipes r ON r.user_id = u.id
LEFT JOIN (
    SELECT recipe_id, COUNT(*) AS likes_count
    FROM likes
    WHERE liked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY recipe_id
) likes_agg ON likes_agg.recipe_id = r.id
LEFT JOIN (
    SELECT recipe_id, COUNT(*) AS bookmarks_count
    FROM bookmarks
    WHERE bookmarked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY recipe_id
) bookmarks_agg ON bookmarks_agg.recipe_id = r.id
WHERE r.published_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
  AND r.status = 'published'
GROUP BY u.id, u.email, u.first_name, u.last_name
ORDER BY total_likes DESC, total_bookmarks DESC, recipes_published DESC
LIMIT 10;
```

**Що демонструє:**
- Subquery з агрегацією як «derived table» для JOIN
- Multiple tie-breakers в ORDER BY

---

## 6. Загальні запити (RBAC, audit)

### 6.1. Перевірка permission для user

```sql
SELECT EXISTS (
    SELECT 1
    FROM user_roles ur
    JOIN role_permissions rp ON rp.role_id = ur.role_id
    JOIN permissions p ON p.id = rp.permission_id
    WHERE ur.user_id = :user_id
      AND p.code = :permission_code
      AND ur.granted_at <= NOW()
) AS has_permission;
```

**Що демонструє:**
- EXISTS як boolean check
- Ланцюг JOIN для RBAC (user → roles → permissions)

---

### 6.2. Audit trail для сутності

```sql
SELECT
    al.id, al.action, al.created_at,
    al.old_values, al.new_values,
    al.ip_address,
    u.email AS performed_by_email
FROM audit_log al
LEFT JOIN users u ON u.id = al.user_id
WHERE al.entity_type = :entity_type  -- 'orders', 'users', etc.
  AND al.entity_id = :entity_id
ORDER BY al.created_at DESC
LIMIT 100;
```

**Що демонструє:**
- Generic audit table з polymorphic entity_type/entity_id
- JSON fields для гнучкого зберігання діффа

---

### 6.3. Rate limiting — перевірка login attempts

```sql
-- Скільки невдалих спроб за останні 15 хвилин з одного IP?
SELECT COUNT(*) AS failed_attempts
FROM login_attempts
WHERE ip_address = :ip
  AND success = FALSE
  AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE);

-- Якщо > 5 → заблокувати IP на годину
```

**Що демонструє:**
- Time window для rate limiting
- Основа для brute-force protection

---

## Як використовувати на захисті

1. **Підготуйте 5-7 запитів** зі свого варіанту — саме ті що демонструють ключові зв'язки.
2. Запустіть їх **заздалегідь** на реальних seed-даних — підготуйте скріншоти результату.
3. Для кожного запиту — знайте відповідь на:
   - Які JOIN використовуються і чому?
   - Де індекс допомагає? Зробіть `EXPLAIN` і покажіть `ref`/`range`/`index`.
   - Що буде при 1 млн записів? Де bottleneck?
   - Як запобігти N+1?

## Анти-шаблони у запитах

- `SELECT *` замість явного списку полів
- Конкатенація рядків замість prepared statements (SQL Injection!)
- LEFT JOIN без потреби (коли INNER JOIN достатньо)
- Запити в циклі (N+1) замість одного з JOIN
- Відсутність LIMIT при показі списків
- OR замість UNION для різних умов з однакової таблиці (UNION швидше з індексами)

## Посилання

- Повні схеми: [schemas.md](schemas.md)
- Помилки у запитах: [typical-mistakes.md § 7](typical-mistakes.md#7-запити-для-lr5)
- Eволюція ЛР4→ЛР5→ЛР6: [evolution-path.md](evolution-path.md)
