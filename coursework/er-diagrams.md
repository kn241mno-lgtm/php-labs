# ER-діаграми референсних варіантів

> **Призначення:** візуалізація схем БД у Mermaid `erDiagram` для 7 еталонних варіантів курсової.
> Базується на DDL зі [schemas.md](schemas.md). Для швидкої орієнтації студента перед читанням повного DDL.
>
> **Як використовувати:** відкрийте файл у редакторі з підтримкою Mermaid (VS Code + Mermaid Preview, GitHub, Obsidian) — діаграми рендеряться автоматично.
> Для курсової: скопіюйте Mermaid-код у свій документ, додайте заголовок «Рис. X.Y — ER-діаграма БД».

## Зміст

1. [v1 Книгарня (E-commerce)](#1-v1-книгарня-e-commerce)
2. [v4 Піцерія (E-commerce + customization)](#2-v4-піцерія-e-commerce--customization)
3. [v3 Кінотеатр (Booking + race condition)](#3-v3-кінотеатр-booking--race-condition)
4. [v14 Стоматологія (Booking + medical)](#4-v14-стоматологія-booking--medical)
5. [v7 Бібліотека (Catalog + lifecycle)](#5-v7-бібліотека-catalog--lifecycle)
6. [v20 Пральня (Service-order + status log)](#6-v20-пральня-service-order--status-log)
7. [v30 Кулінарний блог (UGC + social)](#7-v30-кулінарний-блог-ugc--social)
8. [Крос-модулі (RBAC + audit)](#8-крос-модулі-rbac--audit)

---

## 1. v1 Книгарня (E-commerce)

**Ключові сутності:** users, books, orders, cart_items + категоризація, автори, відгуки, купони.
**Ключові зв'язки:** N↔N (books ↔ authors), 1↔N (orders → order_items), soft FK (reviews → users).

```mermaid
erDiagram
    USERS ||--o{ ADDRESSES : has
    USERS ||--o{ ORDERS : places
    USERS ||--o{ CART_ITEMS : owns
    USERS ||--o{ REVIEWS : writes
    USERS ||--o{ WISHLIST : has
    USERS ||--o{ USER_ROLES : assigned
    ROLES ||--o{ USER_ROLES : grants

    CATEGORIES ||--o{ CATEGORIES : parent
    CATEGORIES ||--o{ BOOKS : contains
    PUBLISHERS ||--o{ BOOKS : publishes
    BOOKS ||--o{ BOOK_AUTHORS : by
    AUTHORS ||--o{ BOOK_AUTHORS : writes
    BOOKS ||--o{ BOOK_IMAGES : has
    BOOKS ||--o{ CART_ITEMS : in
    BOOKS ||--o{ ORDER_ITEMS : ordered
    BOOKS ||--o{ REVIEWS : receives
    BOOKS ||--o{ WISHLIST : listed

    ORDERS ||--o{ ORDER_ITEMS : contains
    ORDERS ||--o{ PAYMENTS : paid_by
    ORDERS }o--|| ADDRESSES : ships_to
    ORDERS }o--o| COUPONS : applies

    USERS {
        int id PK
        string email UK
        string password_hash
        string first_name
        string last_name
        string phone
        datetime created_at
        datetime deleted_at
    }
    BOOKS {
        int id PK
        int category_id FK
        int publisher_id FK
        string isbn UK
        string title
        text description
        decimal price
        int stock
        int year
        int pages
        datetime deleted_at
    }
    ORDERS {
        int id PK
        int user_id FK
        int address_id FK
        int coupon_id FK
        string order_number UK
        enum current_status
        decimal total_amount
        datetime created_at
    }
    ORDER_ITEMS {
        int id PK
        int order_id FK
        int book_id FK
        int quantity
        decimal unit_price
    }
    REVIEWS {
        int id PK
        int book_id FK
        int user_id FK
        int rating
        text comment
        datetime created_at
    }
```

**Нотатки для захисту:**

- `deleted_at` на `users` + `books` — soft delete, збереження історичної цілісності замовлень.
- `UNIQUE(email)` на users + `UNIQUE(isbn)` на books — запобігає дублюванню на рівні БД.
- `book_authors` (junction) — N↔N з composite PK `(book_id, author_id)`.
- `current_status` як ENUM для швидкого запиту + окрема таблиця `order_status_log` для історії.

---

## 2. v4 Піцерія (E-commerce + customization)

**Розширення над v1:** кастомізація позицій (інгредієнти), зони доставки, тайм-слоти.
**Ключові зв'язки:** N↔N (pizzas ↔ ingredients), кастомізація на рівні order_items.

```mermaid
erDiagram
    USERS ||--o{ ADDRESSES : has
    USERS ||--o{ ORDERS : places
    CATEGORIES ||--o{ PIZZAS : contains

    PIZZAS ||--o{ PIZZA_INGREDIENTS : has
    INGREDIENTS ||--o{ PIZZA_INGREDIENTS : used_in
    PIZZAS ||--o{ ORDER_ITEMS : ordered

    ORDERS ||--o{ ORDER_ITEMS : contains
    ORDERS }o--|| DELIVERY_ZONES : delivered_to
    ORDER_ITEMS ||--o{ ORDER_ITEM_CUSTOMIZATIONS : modified
    INGREDIENTS ||--o{ ORDER_ITEM_CUSTOMIZATIONS : added_or_removed

    PIZZAS {
        int id PK
        int category_id FK
        string name
        text description
        decimal base_price
        int size_cm
        enum base_type
        datetime deleted_at
    }
    INGREDIENTS {
        int id PK
        string name UK
        decimal price_per_unit
        enum category
        boolean is_vegan
    }
    PIZZA_INGREDIENTS {
        int pizza_id PK_FK
        int ingredient_id PK_FK
        decimal default_quantity
        boolean is_removable
    }
    DELIVERY_ZONES {
        int id PK
        string name
        decimal delivery_fee
        int estimated_minutes
        decimal min_order
    }
    ORDER_ITEM_CUSTOMIZATIONS {
        int id PK
        int order_item_id FK
        int ingredient_id FK
        enum action
        decimal price_delta
    }
```

**Нотатки:**

- `pizza_ingredients` — default склад (що йде в піцу стандартно).
- `order_item_customizations` — що клієнт додав/прибрав для конкретного замовлення (`action` = ADD/REMOVE).
- `delivery_zones.min_order` — бізнес-правило мінімального замовлення.
- Enum `base_type` (traditional/thin/thick) — для фільтрації у меню.

---

## 3. v3 Кінотеатр (Booking + race condition)

**Ключова особливість:** запобігання подвійного бронювання через `UNIQUE(session_id, seat_id)`.
**Ключові зв'язки:** halls → seats, movies → sessions, tickets = композитний бронюючий ключ.

```mermaid
erDiagram
    USERS ||--o{ TICKETS : buys
    USERS ||--o{ REVIEWS : writes

    HALLS ||--o{ SEATS : contains
    HALLS ||--o{ SESSIONS : hosts

    MOVIES ||--o{ SESSIONS : scheduled
    MOVIES ||--o{ MOVIE_GENRES : tagged
    GENRES ||--o{ MOVIE_GENRES : groups
    MOVIES ||--o{ REVIEWS : receives

    SESSIONS ||--o{ TICKETS : booked
    SEATS ||--o{ TICKETS : assigned

    HALLS {
        int id PK
        string name UK
        int rows_count
        int seats_per_row
        int total_capacity
    }
    SEATS {
        int id PK
        int hall_id FK
        int row_number
        int seat_number
        enum seat_type
        decimal price_multiplier
    }
    MOVIES {
        int id PK
        string title
        text description
        int duration_minutes
        int release_year
        enum age_rating
        decimal base_price
        datetime deleted_at
    }
    SESSIONS {
        int id PK
        int movie_id FK
        int hall_id FK
        datetime start_at
        decimal price
        enum status
    }
    TICKETS {
        int id PK
        int session_id FK
        int seat_id FK
        int user_id FK
        decimal price
        enum status
        string booking_code UK
        datetime created_at
    }
```

**Ключові constraints для захисту:**

- `UNIQUE(session_id, seat_id)` у tickets → БД не дозволить другого квитка на те саме місце в тому ж сеансі навіть при race condition.
- `seats.price_multiplier` (1.0 для звичайних, 1.5 для VIP) — множник до `sessions.price`.
- `tickets.status` ENUM: reserved → paid → used, або reserved → cancelled.
- `sessions.status`: scheduled/cancelled/completed — для фільтрації активних сеансів.

---

## 4. v14 Стоматологія (Booking + medical)

**Розширення над v3:** пацієнти ≠ users (пацієнта може вести опікун), медичні записи, рецепти.
**Ключові зв'язки:** doctors (users+role), patients, appointments, medical_records, prescriptions.

```mermaid
erDiagram
    USERS ||--o{ PATIENTS : owns_profile
    USERS ||--o{ APPOINTMENTS : as_doctor

    PATIENTS ||--o{ APPOINTMENTS : as_patient
    PATIENTS ||--o{ MEDICAL_RECORDS : has_history

    SERVICES ||--o{ APPOINTMENTS : booked_for
    SERVICES ||--o{ SERVICE_CATEGORIES : belongs_to

    APPOINTMENTS ||--o{ MEDICAL_RECORDS : produces
    APPOINTMENTS ||--o{ PRESCRIPTIONS : generates

    MEDICAL_RECORDS ||--o{ PRESCRIPTIONS : contains

    PATIENTS {
        int id PK
        int user_id FK
        string first_name
        string last_name
        date birth_date
        enum gender
        string insurance_number UK
        text allergies
        text chronic_conditions
    }
    SERVICES {
        int id PK
        int category_id FK
        string name
        text description
        decimal price
        int duration_minutes
    }
    APPOINTMENTS {
        int id PK
        int patient_id FK
        int doctor_id FK
        int service_id FK
        datetime scheduled_at
        int duration_minutes
        enum status
        text notes
    }
    MEDICAL_RECORDS {
        int id PK
        int patient_id FK
        int appointment_id FK
        int doctor_id FK
        date record_date
        text diagnosis
        text treatment
        text x_ray_urls
    }
    PRESCRIPTIONS {
        int id PK
        int medical_record_id FK
        int doctor_id FK
        string medication_name
        string dosage
        int duration_days
        date issued_at
    }
```

**Нотатки:**

- `patients` окремо від `users` — бо дитина може бути пацієнтом, а батьки мають аккаунт.
- `UNIQUE(doctor_id, scheduled_at)` для appointments — один лікар = одна зустріч в конкретний слот.
- `medical_records.x_ray_urls` як JSON array — гнучке зберігання медіа.
- `prescriptions.duration_days` + `issued_at` — для автоматичного розрахунку дати закінчення рецепту.

---

## 5. v7 Бібліотека (Catalog + lifecycle)

**Особливість:** кожен примірник (copy) окремо, життєвий цикл позички (loan).
**Ключові зв'язки:** books → copies → loans, reservations (черга очікування).

```mermaid
erDiagram
    USERS ||--o{ LOANS : borrows
    USERS ||--o{ RESERVATIONS : queues
    USERS ||--o{ FAVORITES : saves

    AUTHORS ||--o{ BOOK_AUTHORS : writes
    BOOKS ||--o{ BOOK_AUTHORS : by
    CATEGORIES ||--o{ BOOKS : contains
    PUBLISHERS ||--o{ BOOKS : publishes

    BOOKS ||--o{ JOURNAL_COPIES : has_copies
    BOOKS ||--o{ RESERVATIONS : reserved
    BOOKS ||--o{ FAVORITES : favorited

    JOURNAL_COPIES ||--o{ LOANS : lent_as

    BOOKS {
        int id PK
        int category_id FK
        int publisher_id FK
        string isbn UK
        string title
        text description
        int year
        int pages
        decimal rating
    }
    JOURNAL_COPIES {
        int id PK
        int book_id FK
        string inventory_number UK
        enum condition
        enum status
        date acquired_at
    }
    LOANS {
        int id PK
        int copy_id FK
        int user_id FK
        date borrowed_at
        date due_at
        date returned_at
        decimal fine_amount
        enum status
    }
    RESERVATIONS {
        int id PK
        int book_id FK
        int user_id FK
        datetime reserved_at
        int queue_position
        enum status
    }
```

**Ключові моменти:**

- `journal_copies` — кожен фізичний примірник окремо (inventory_number = штрих-код).
- `copies.status`: available/loaned/damaged/lost — для швидкого фільтру «що можна взяти».
- `loans.due_at` + `returned_at` — для автоматичного розрахунку штрафу (`fine_amount`).
- `reservations.queue_position` — якщо книга зайнята, черга очікування (auto-recalc on return).

---

## 6. v20 Пральня (Service-order + status log)

**Особливість:** тригер автоматично записує історію статусів у `order_status_log`.
**Ключові зв'язки:** orders → items → services, pickups (адреса забрати/доставити).

```mermaid
erDiagram
    USERS ||--o{ ORDERS : places

    SERVICE_CATEGORIES ||--o{ SERVICES : contains
    SERVICES ||--o{ ORDER_ITEMS : ordered

    ORDERS ||--o{ ORDER_ITEMS : contains
    ORDERS ||--o{ ORDER_STATUS_LOG : audited
    ORDERS ||--o{ PICKUPS : scheduled
    ORDERS ||--o{ PAYMENTS : paid

    ORDERS {
        int id PK
        int user_id FK
        string order_number UK
        enum current_status
        decimal total_amount
        datetime accepted_at
        datetime ready_at
        datetime delivered_at
        text notes
    }
    SERVICES {
        int id PK
        int category_id FK
        string name
        decimal price_per_unit
        enum unit_type
        int estimated_hours
    }
    ORDER_ITEMS {
        int id PK
        int order_id FK
        int service_id FK
        decimal quantity
        decimal unit_price
        decimal total_price
    }
    ORDER_STATUS_LOG {
        int id PK
        int order_id FK
        int changed_by_user_id FK
        enum old_status
        enum new_status
        datetime changed_at
        text comment
    }
    PICKUPS {
        int id PK
        int order_id FK
        enum pickup_type
        string address
        datetime scheduled_at
        datetime completed_at
        enum status
    }
```

**Ключові моменти:**

- `order_status_log` наповнюється через `AFTER UPDATE` тригер на `orders` — студент показує на захисті.
- `pickups.pickup_type` = collection (забрати) / delivery (доставити) — один order може мати дві pickups.
- `services.unit_type` ENUM: kg/item/m2 — різні одиниці для різних послуг (прання кг, прасування штук).
- `orders.accepted_at/ready_at/delivered_at` — ключові timestamps для SLA-звітності.

---

## 7. v30 Кулінарний блог (UGC + social)

**Особливість:** user-generated content, social features (likes, bookmarks, follows, nested comments).
**Ключові зв'язки:** self-reference (comments.parent_id, users ↔ users через follows), N↔N (recipes ↔ tags).

```mermaid
erDiagram
    USERS ||--o{ RECIPES : authors
    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ LIKES : reacts
    USERS ||--o{ BOOKMARKS : saves
    USERS ||--o{ FOLLOWS : follower
    USERS ||--o{ FOLLOWS : followee

    RECIPES ||--o{ RECIPE_INGREDIENTS : needs
    RECIPES ||--o{ RECIPE_STEPS : has_steps
    RECIPES ||--o{ RECIPE_TAGS : tagged
    RECIPES ||--o{ COMMENTS : discussed
    RECIPES ||--o{ LIKES : liked
    RECIPES ||--o{ BOOKMARKS : saved

    INGREDIENTS ||--o{ RECIPE_INGREDIENTS : used_in
    TAGS ||--o{ RECIPE_TAGS : groups
    CATEGORIES ||--o{ RECIPES : contains

    COMMENTS ||--o{ COMMENTS : parent

    RECIPES {
        int id PK
        int user_id FK
        int category_id FK
        string title
        string slug UK
        text description
        text cooking_instructions
        int prep_minutes
        int cook_minutes
        int servings
        enum difficulty
        string cover_image_url
        enum status
        datetime published_at
        datetime deleted_at
    }
    RECIPE_INGREDIENTS {
        int id PK
        int recipe_id FK
        int ingredient_id FK
        decimal quantity
        string unit
        text notes
    }
    RECIPE_STEPS {
        int id PK
        int recipe_id FK
        int step_order
        text description
        string image_url
        int duration_seconds
    }
    COMMENTS {
        int id PK
        int recipe_id FK
        int user_id FK
        int parent_id FK
        text body
        datetime created_at
        datetime deleted_at
    }
    FOLLOWS {
        int follower_id PK_FK
        int followee_id PK_FK
        datetime followed_at
    }
    LIKES {
        int user_id PK_FK
        int recipe_id PK_FK
        datetime liked_at
    }
```

**Ключові моменти:**

- `comments.parent_id` (self-FK) — nested коментарі (Reddit-style).
- `follows` — composite PK `(follower_id, followee_id)` + CHECK `follower_id != followee_id`.
- `likes`/`bookmarks` — composite PK для запобігання дублів.
- `recipes.slug` UNIQUE — для SEO-friendly URLs (`/recipes/borsch-po-kiivsky`).
- `recipes.status` ENUM: draft/published/archived — lifecycle поста.

---

## 8. Крос-модулі (RBAC + audit)

**Застосування:** однакова структура для всіх варіантів. Не малюється окремо для кожної ER, але **обов'язково згадується** в курсовій.

```mermaid
erDiagram
    USERS ||--o{ USER_ROLES : has
    ROLES ||--o{ USER_ROLES : granted_to
    ROLES ||--o{ ROLE_PERMISSIONS : owns
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : granted_via

    USERS ||--o{ AUDIT_LOG : performs
    USERS ||--o{ LOGIN_ATTEMPTS : tried
    USERS ||--o{ PASSWORD_RESETS : requested

    ROLES {
        int id PK
        string name UK
        string description
    }
    PERMISSIONS {
        int id PK
        string code UK
        string description
    }
    USER_ROLES {
        int user_id PK_FK
        int role_id PK_FK
        datetime granted_at
    }
    ROLE_PERMISSIONS {
        int role_id PK_FK
        int permission_id PK_FK
    }
    AUDIT_LOG {
        int id PK
        int user_id FK
        string action
        string entity_type
        int entity_id
        json old_values
        json new_values
        string ip_address
        datetime created_at
    }
    LOGIN_ATTEMPTS {
        int id PK
        int user_id FK
        string email
        string ip_address
        boolean success
        datetime attempted_at
    }
```

**Навіщо показати на захисті:**

- **RBAC** (user_roles + role_permissions) — масштабовано, додавання нової ролі не вимагає змін коду.
- **audit_log** — хто/що/коли змінив, обов'язковий для бізнес-систем (GDPR-compliance).
- **login_attempts** — для запобігання brute-force (rate limiting на рівні додатку).
- **password_resets** з `expires_at` + `used_at` — одноразові токени з TTL.

---

## Як рендерити Mermaid

- **VS Code:** встановити розширення `Markdown Preview Mermaid Support` → Ctrl+Shift+V.
- **GitHub/GitLab:** ренедриться автоматично в `.md` файлах.
- **Obsidian:** нативна підтримка.
- **Експорт у PNG:** [mermaid.live](https://mermaid.live) → paste → Export.
- **У курсову (Word/docx):** експортувати PNG → вставити як «Рис. X.Y — ER-діаграма БД».

## Посилання

- Повний DDL: [schemas.md](schemas.md)
- Чекліст перед здачею: [checklist-lr4.md](checklist-lr4.md)
- Типові помилки: [typical-mistakes.md](typical-mistakes.md)
