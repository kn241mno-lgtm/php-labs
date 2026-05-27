# Каталог фіч для курсової

**Навіщо документ.** Меню функцій, які можна реалізувати в курсовій — від мінімуму до просунутого рівня. Беріть **все з 🟢 для свого типу** (це must), потім **1-3 з 🟡** для балів, **0-1 з 🔴** для максимуму.

> **Де починати** — [functionality-flow.md](functionality-flow.md) визначить ваш тип і обов'язкові блоки.
> **Як реалізовувати** — [system-design.md](system-design.md) дасть структуру коду.

## 0. Стек: Laravel або vanilla PHP (як ЛР5)

Курсову можна виконувати **в двох режимах**:

| Стек | Макс. балів | Коли обирати | На чому будується |
|------|-------------|--------------|-------------------|
| **🚀 Laravel** (рекомендовано) | **100** | Хочете максимум балів + готові інвестувати 2-3 год у налаштування Breeze/Eloquent | `composer create-project laravel/laravel`. Всі extended/advanced фічі — легко. |
| **🛠 Vanilla PHP (MVC з ЛР5)** | **~80** | Не встигаєте опанувати фреймворк; хочете максимально переписати з ЛР5 | Клас `Application`, `Router`, `Controller`, `View`, власний `Database` PDO. Жодного Eloquent/Blade/Migrations. |

**Техніки, позначені нижче, мають варіанти:**

- Синтаксис `Eloquent` / `FormRequest` / `Mail::to()` / `session()` / `@foreach` — **Laravel-стиль**.
- Для vanilla PHP відповідники: PDO prepared statements, ручна валідація в контролері, `mail()` або PHPMailer, `$_SESSION`, `<?php foreach ?>`.

### ⛔ MVC — ОБОВ'ЯЗКОВА вимога для обох стеків

**Курсова без явного поділу Model–View–Controller не приймається.** Без зайвого вибору архітектури — три шари мають бути присутні фізично як окремі папки/класи.

**На Laravel** (вже з коробки):
- `app/Models/Product.php`, `app/Models/Order.php` — Eloquent моделі з відношеннями (`hasMany`, `belongsTo`)
- `app/Http/Controllers/ProductController.php` — лише оркестрація: запит → модель → view
- `resources/views/products/index.blade.php` — лише презентація
- **❌ Заборонено:** SQL у контролері/в'юшці, бізнес-логіка у Blade.

**На vanilla PHP** (доповнюється до ЛР5):

В [ЛР5](../lr5/assignment.md) є **тільки Controller + View** — SQL пишеться прямо в контролерах через `classes/Database.php`. Для курсової **обов'язково** додати Model-шар:

```
coursework/
├── classes/
│   ├── Application.php     ← з ЛР4
│   ├── Router.php          ← з ЛР4
│   ├── Request.php         ← з ЛР4
│   ├── Controller.php      ← з ЛР4
│   ├── View.php            ← з ЛР4
│   ├── Database.php        ← з ЛР5
│   └── Model.php           ← обов'язково: базовий клас з find/all/save/delete
├── models/                 ← обов'язкова папка
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── User.php
│   └── Category.php
├── controllers/
└── views/
```

Базовий `Model.php` (мінімум методів):
- `public static function all(): array`
- `public static function find(int $id): ?self`
- `public function save(): bool` (insert або update)
- `public function delete(): bool`
- `public function <relation>(): array` — JOIN через Database

**Правила, що перевіряються на захисті** (для обох стеків):
1. У контролері — **жодного `SELECT/INSERT/UPDATE/DELETE`**. Тільки виклики моделі.
2. У view — **жодного запиту до БД**. Тільки відображення переданих даних.
3. У моделі — **жодного echo/HTML**. Тільки робота з даними.
4. Кожна сутність БД (3+) має **свою модель**.
5. Зв'язки між сутностями — через методи моделі (`$order->items()`, `$product->category()`).

**Обмеження vanilla PHP** (чого **не вийде** зробити або ціна занадто висока):

| Фіча | На vanilla | Коментар |
|------|------------|----------|
| Eloquent зв'язки (`hasMany`, `belongsToMany`) | Руками через PDO + JOIN | OK, але писати більше |
| Міграції через `artisan` | SQL-файли `database/schema.sql` | Студент створює БД через phpMyAdmin або скрипт |
| Breeze (auth scaffolding) | Власна форма + `password_hash` (з ЛР5) | Вже є в ЛР5 |
| Blade `@extends/@include` | `include`/`require` + `ob_start` (з ЛР4 `renderDemoLayout`) | OK |
| Policy / Middleware | Перевірки в контролері через `if (!$_SESSION['admin'])` | OK, але дублюється |
| Queue / Scheduler | ❌ немає | Усі advanced фічі з сповіщеннями за розкладом — **недоступні** |
| Sanctum API tokens | ❌ краще не робити | API можна, але auth через сесію, не tokens |
| Socialite (OAuth) | ❌ вручну через cURL | Занадто складно — не братимте |
| Паспорт / 2FA пакети | ❌ | Можна тільки власну реалізацію |
| Spatie Permission | Ручна реалізація ролей через `users.role` | OK |

**Практичний поріг балів для vanilla:** 70-80 (мінімум + 2-3 🟡 з простих розділів). Без queue та пакетів **більшість 🔴 недоступна**.

**Рекомендація:**
- Якщо ваша тема — **Shop** або **Booking** — беріть **Laravel**. Там дуже багато чого з коробки.
- Якщо **Catalog** або **Community** мінімум — можна vanilla, але максимум балів буде 75-80.
- Якщо **Internal/CRM** — тільки Laravel (middleware + policies критичні).

Далі в каталозі: всі фічі реалізовні на Laravel. Ті, що **не-vanilla-сумісні**, позначені 🚫v.

## Легенда

| Тег | Рівень | Що означає |
|-----|--------|------------|
| 🟢 | **Мінімум** | Для 60-70 балів. Частина мінімального набору для цього типу. |
| 🟡 | **Extended** | +5-10 балів за штуку. Заявляйте 1-3 фічі понад мінімум. |
| 🔴 | **Advanced** | +10-15 балів. Складно, довго, але сильно виділяє серед інших. |
| ⭐ | **Wow-фактор** | Не обов'язково складно, але візуально/логічно вражає на захисті. |
| 🚫v | **Laravel-only** | Неможливо (або економічно невиправдано) робити на vanilla PHP. Оберіть альтернативу або Laravel-стек. |

Нижче — фічі згруповані за категоріями. Кожен рядок: **назва** · рівень · для яких типів · коротка техніка · effort (год).

---

## 1. Коментарі та дискусії

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Лінійні коментарі (плоский список) | 🟢 | D (UGC) | `comments: id, post_id, user_id, body, created_at` | 3-4 г |
| Коментарі до будь-якої сутності (polymorphic) 🚫v | 🟡 | A, B, C, D | `commentable_type`, `commentable_id` (morphTo) | 4-6 г |
| Деревовидні треди (nested replies) ⭐ | 🔴 | D | `parent_id` self-FK + рекурсивний рендер / adjacency list | 6-8 г |
| Редагування власного коментаря | 🟡 | всі | Policy `update` + `updated_at != created_at` маркер | 1-2 г |
| Soft delete коментарів (позначка «видалено») | 🟡 | D, E | Eloquent SoftDeletes (Laravel) / `deleted_at` + WHERE IS NULL (vanilla) | 1 г |
| Markdown/BBCode у коментарях | 🟡 | D | `league/commonmark` + escape HTML | 2-3 г |
| Згадки @username з автокомпліт ⭐ | 🔴 | D | JS autocomplete + парсинг + notification | 6-8 г |
| Емодзі-реакції на коментарі (👍❤️😂) ⭐ | 🟡 | D | `reactions: user+comment+emoji` unique | 3-4 г |

---

## 2. Рейтинги та голосування

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Лайки (one-click, toggle) | 🟢 | D | `likes: user_id+item_id` unique + COUNT | 2-3 г |
| Зіркова оцінка 1-5 від user | 🟡 | A, C, D | `ratings: user+item+stars (1-5)` + AVG | 3-4 г |
| Up/Down голосування (Reddit-стиль) | 🟡 | D | `votes: user+item+value (+1/-1)` + SUM | 3-4 г |
| Відгуки з текстом + оцінкою | 🟡 | A, B | `reviews: user+product+rating+text` | 4-5 г |
| Верифіковані відгуки (тільки покупці) ⭐ | 🔴 | A | Перевірка: user має order з цим product | 2-3 г |
| Корисність відгуку («допомогло N людям») | 🔴 | A | Nested voting на reviews | 3-4 г |
| Зважений рейтинг (IMDb-формула) ⭐ | 🔴 | C | `(v/(v+m))*R + (m/(v+m))*C`, де v=votes | 2-3 г |
| Топ-100 / рейтинг-борд | 🟡 | C, D | ORDER BY avg_rating DESC LIMIT 100 | 1-2 г |

---

## 3. Соціальні фічі

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Публічний профіль користувача | 🟢 | D | `/users/{id}` з постами/коментарями | 2-3 г |
| Аватар (upload) | 🟡 | всі | `users.avatar_path` + placeholder | 2 г |
| Follow/Підписки на авторів ⭐ | 🔴 | D | `follows: follower_id + followed_id` unique | 4-5 г |
| Стрічка friends-only / підписок | 🔴 | D | Post `visibility: public/followers` + JOIN | 4-6 г |
| Друзі (взаємна згода) | 🔴 | D | `friendships: user1+user2+status` | 6-8 г |
| Приватні повідомлення (DM) ⭐ | 🔴 | D, E | `messages: from+to+body+read_at` | 8-12 г |
| Online/Offline статус | 🔴 | E | `users.last_seen_at` + heartbeat cron | 3-4 г |
| Блокування користувачів | 🔴 | D | `blocks: blocker+blocked` unique | 2-3 г |

---

## 4. Сповіщення

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Flash-повідомлення (після дії) | 🟢 | всі | `session()->flash()` + Bootstrap alert | 1 г |
| In-app notifications (дзвіночок з лічильником) | 🟡 | D, E | `notifications` таблиця (Laravel нативно) | 3-4 г |
| Email на реєстрацію | 🟡 | всі | `Mail::to()` + Mailtrap + Notification клас | 2-3 г |
| Email про нове замовлення / бронювання | 🟡 | A, B | Event → Listener → Notification | 3-4 г |
| Нагадування за 24 год до бронювання ⭐ 🚫v | 🔴 | B | Scheduled command + cron (Laravel Scheduler) | 4-6 г |
| Telegram-бот сповіщення ⭐ | 🔴 | A, B | Telegram Bot API + webhook (є й на vanilla) | 6-10 г |
| Browser push (Service Worker) | 🔴 | D | Web Push API + VAPID | 8-12 г |
| Digest-розсилка (щотижня) 🚫v | 🔴 | D | Scheduled + queue + Mail | 4-6 г |

---

## 5. Пошук та фільтри

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Пошук по назві (GET q, LIKE) | 🟢 | всі | `WHERE name LIKE '%{q}%'` | 1 г |
| Фільтр за 1 категорією | 🟢 | A, B, C | `?category=N` → `WHERE category_id` | 1 г |
| Мультифільтр (2-5 полів) | 🟡 | A, B, C | Форма GET + query builder | 3-4 г |
| Сортування (ціна ↑↓, дата, рейтинг) | 🟡 | A, C | `?sort=price_asc` → `ORDER BY` | 1-2 г |
| Діапазони (ціна від-до, дата від-до) | 🟡 | A, B, C | `whereBetween()` | 1-2 г |
| Full-text search (MySQL FULLTEXT) ⭐ | 🔴 | C, D | `MATCH ... AGAINST` SQL (vanilla + Laravel). `laravel/scout` тільки на Laravel. | 4-8 г |
| Автокомпліт у пошуку ⭐ | 🔴 | A, C | AJAX /api/search/suggest + debounce | 4-6 г |
| Збережені пошуки (saved searches) | 🔴 | E | `saved_searches: user+params+name` | 3-4 г |
| Пошук з пам'яттю (остання історія) | 🟡 | D | cookie / localStorage | 1-2 г |

---

## 6. Організація контенту

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Категорії (один-до-багатьох) | 🟢 | A, B, C | `category_id` FK | 2 г |
| Вкладені категорії (дерево) ⭐ | 🔴 | A, C | `parent_id` self-FK + breadcrumbs | 4-6 г |
| Теги (багато-до-багатьох) | 🟡 | C, D | pivot `taggables` або `post_tag` | 3-4 г |
| Колекції / плейлисти користувача | 🔴 | C, D | `collections` + `collection_items` | 4-6 г |
| Закладки (bookmarks/favorites) | 🟡 | C, D | `favorites: user+item` unique | 2-3 г |
| Історія переглядів | 🟡 | A, C | `view_history: user+item+viewed_at` | 2 г |
| Wish-list ⭐ | 🟡 | A | те саме що favorites, для товарів | 2 г |
| Drag-and-drop reorder колекції | 🔴 | D | SortableJS + `position` поле + AJAX | 4-6 г |

---

## 7. Магазин-специфічні (Shop A)

| Фіча | Рівень | Техніка | Effort |
|------|--------|---------|--------|
| Кошик (session-based) | 🟢 | `session('cart', [])` + add/remove/update | 3-4 г |
| Кошик у БД для залогінених | 🟡 | `carts` + `cart_items` tables | 2-3 г |
| Checkout форма (адреса, доставка) | 🟢 | FormRequest + створення order+items | 3-4 г |
| Статуси замовлення (pending→paid→shipped→done) | 🟢 | `orders.status` enum + admin transitions | 2 г |
| Промокоди (знижки) ⭐ | 🟡 | `promo_codes: code+discount_percent+expires_at` | 3-4 г |
| Подарункові сертифікати | 🔴 | `gift_cards: code+balance+owner_id` | 4-6 г |
| Система лояльності (бонусні бали) | 🔴 | `user.bonus_points` + earn/spend logic | 4-6 г |
| Рекомендовані товари ⭐ | 🔴 | «Ті, хто купив X, купили Y» — через `order_items` JOIN | 6-8 г |
| «Переглянуті разом» (related viewed) | 🔴 | `view_history` JOIN | 3-4 г |
| Порівняння товарів (до 4) | 🟡 | session + таблиця характеристик | 3-4 г |
| Повернення товару (RMA) | 🔴 | `returns: order_item+reason+status` | 4-6 г |
| Відгук тільки після покупки ⭐ | 🟡 | Policy: user має order_item.product_id | 2 г |
| Склад (кількість, резервування) | 🟡 | `products.stock` + decrement на order | 2-3 г |
| Попередження «залишилось N шт» | 🟡 | `stock <= 5` → бейдж у UI | 1 г |
| Підписка на товар (notify when in stock) | 🔴 | `stock_alerts: user+product` + cron | 3-4 г |

---

## 8. Бронювання-специфічні (Booking B)

| Фіча | Рівень | Техніка | Effort |
|------|--------|---------|--------|
| Вибір дати + часу зі списку | 🟢 | `<input type="date">` + `<select>` годин | 2 г |
| Перевірка зайнятості слоту | 🟢 | UNIQUE (master, date, time) + try/catch | 2 г |
| Календар-візуалізація (FullCalendar.js) ⭐ | 🟡 | FullCalendar + AJAX events | 4-6 г |
| Робочий графік майстра (день/година on-off) | 🟡 | `schedules: master+weekday+start+end` | 3-4 г |
| Вихідні/свята (блокування) | 🟡 | `holidays` таблиця + перевірка | 2 г |
| Регулярні бронювання (щотижня/місяць) 🚫v | 🔴 | `bookings.recurrence_rule` (iCal RRULE) + queue для генерації | 6-10 г |
| Waitlist (лист очікування) ⭐ | 🔴 | `waitlist: user+service+priority` | 4-6 г |
| Політика скасування (за X годин) | 🟡 | check `now() < booking.time - X hours` | 1-2 г |
| Передплата / депозит | 🔴 | `booking.deposit_paid` + payment | 4-6 г |
| Нагадування email/Telegram | 🟡 | Scheduled task | 3-4 г |
| Груповий запис (кілька учасників) | 🔴 | `booking.max_participants` + `participants` pivot | 4-6 г |
| Оцінка після відвідування | 🟡 | Policy: `booking.status == done` | 2-3 г |

---

## 9. Каталог-специфічні (Catalog C)

| Фіча | Рівень | Техніка | Effort |
|------|--------|---------|--------|
| Детальна сторінка елемента | 🟢 | `/items/{id}` | 2 г |
| SEO-friendly URL (slug) | 🟡 | `items.slug` + route `/items/{slug}` | 1 г |
| Пов'язані елементи («similar») | 🟡 | WHERE category_id = ... AND id != ... | 1-2 г |
| Timeline / хронологія | 🟡 | ORDER BY year + групування | 2 г |
| Карта (Google/Leaflet) ⭐ | 🔴 | lat/lng поля + Leaflet.js | 4-6 г |
| Галерея з lightbox | 🟡 | multiple images + PhotoSwipe | 3-4 г |
| Віртуальний тур / 360° | 🔴 | Marzipano / Pannellum.js | 8-12 г |
| Резервування елемента (бібліотека) | 🟡 | `reservations: user+item+status` | 3-4 г |
| Штрафи за прострочку 🚫v | 🔴 | Laravel Scheduler + `fines` таблиця | 3-4 г |
| QR-код на елемент ⭐ | 🟡 | bacon/qr-code package | 1-2 г |

---

## 10. Контент-специфічні (Community D)

| Фіча | Рівень | Техніка | Effort |
|------|--------|---------|--------|
| Rich-text редактор (TinyMCE/Quill) ⭐ | 🟡 | TinyMCE CDN + sanitize HTML | 2-3 г |
| Drafts (чернетки) | 🟡 | `posts.status: draft/published` | 1-2 г |
| Scheduled publish 🚫v | 🔴 | `published_at` + Laravel Scheduler | 3-4 г |
| Версії постів (history) | 🔴 | `post_revisions: post_id+body+created_at` | 4-6 г |
| Cover image + alt text | 🟡 | image upload + `alt` поле | 2 г |
| Опитування (poll) у пості ⭐ | 🔴 | `polls + poll_options + poll_votes` | 4-6 г |
| Читацький час (reading time) | 🟡 | `str_word_count / 200` | 0.5 г |
| «Поділитися» (share buttons) | 🟢 | Facebook/Twitter/Telegram links | 0.5 г |
| Друк / експорт у PDF | 🟡 | dompdf на /posts/{id}/pdf | 2-3 г |
| RSS-фід | 🔴 | /feed.xml route | 2-3 г |

---

## 11. Адмін-панель та модерація

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| CRUD усіх сутностей | 🟢 | всі | Resource controllers | (у мінімумі) |
| Dashboard з лічильниками | 🟢 | E | COUNT(*) по таблицях | 1-2 г |
| Графіки (за тиждень/місяць) ⭐ | 🟡 | A, B, E | Chart.js + GROUP BY date | 3-4 г |
| Soft delete + Trash view | 🟡 | всі | Eloquent SoftDeletes (Laravel) / `deleted_at` поле + IS NULL/NOT NULL (vanilla) | 2 г |
| Відновлення з Trash | 🟡 | всі | `restore()` | 1 г |
| Аудит-лог (audit_log) ⭐ | 🟡 | E | Model observer на created/updated/deleted | 3-4 г |
| Bulk actions (масові дії) | 🔴 | A, E | checkbox + AJAX + transaction | 3-4 г |
| Модерація коментарів (approve/reject) | 🟡 | D | `comments.status` + admin queue | 2-3 г |
| Скарги (reports) | 🔴 | D | `reports: user+content+reason+status` | 3-4 г |
| Бан користувачів | 🟡 | D | `users.banned_at` + middleware | 2 г |
| Ролі через Spatie Permission 🚫v | 🟡 | E | `spatie/laravel-permission`. Vanilla: `users.role` enum + middleware-функція | 2-3 г |
| Імперсонація (login as user) 🚫v | 🔴 | E | `lab404/laravel-impersonate`. Vanilla: `$_SESSION['impersonate_id']` + перевірка | 2 г |

---

## 12. Dashboard, аналітика, звіти

| Фіча | Рівень | Техніка | Effort |
|------|--------|---------|--------|
| Лічильники на головній (total users/orders/posts) | 🟢 | COUNT(*) | 1 г |
| Графік динаміки (за 30 днів) | 🟡 | Chart.js + GROUP BY DATE | 3-4 г |
| Топ-10 списки (найпопулярніше) | 🟡 | ORDER BY count DESC LIMIT 10 | 1-2 г |
| Експорт у CSV | 🟡 | `fputcsv` stream | 1-2 г |
| Експорт у Excel 🚫v | 🔴 | `maatwebsite/excel`. Vanilla: `PhpSpreadsheet` напряму | 2-3 г |
| Експорт у PDF (звіт) | 🔴 | `barryvdh/laravel-dompdf` (Laravel) / `dompdf/dompdf` напряму (vanilla) | 2-3 г |
| Фільтр періоду (date range) | 🟡 | whereBetween | 1 г |
| Порівняння періодів (this vs prev month) | 🔴 | 2 запити + розрахунок diff | 2-3 г |
| Heatmap активності ⭐ | 🔴 | CalHeatmap.js + GROUP BY DATE | 4-6 г |

---

## 13. Комунікація та підтримка

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Контакт-форма (на email) | 🟢 | всі | FormRequest + Mail::to() | 1-2 г |
| FAQ (статичні) | 🟢 | всі | Blade view | 0.5 г |
| FAQ (CRUD у адмінці) | 🟡 | всі | `faqs` таблиця | 2 г |
| Тікет-система ⭐ | 🔴 | E | `tickets + ticket_messages + status` | 8-12 г |
| Live-чат (AJAX polling) | 🔴 | D, E | `messages` table + JS poll every 3s | 6-8 г |
| Live-чат (WebSocket) ⭐ | 🔴 | D, E | Laravel Echo + Pusher | 10-15 г |
| Форум / дискусії | 🔴 | D | `threads + posts` | 8-12 г |

---

## 14. Безпека

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Валідація всіх форм на сервері | 🟢 | всі | FormRequest rules | (мінімум) |
| CSRF захист | 🟢 | всі | Laravel нативно | — |
| XSS захист (escape) | 🟢 | всі | Blade `{{ }}` замість `{!! !!}` | — |
| Хешування паролів | 🟢 | всі | `Hash::make` / `password_hash` | — |
| Rate limiting на login | 🟡 | всі | `throttle:5,1` middleware | 0.5 г |
| Email verification ⭐ 🚫v | 🟡 | всі | Laravel MustVerifyEmail. Vanilla: руками — token у `users.verify_token` + email link | 2 г |
| Password reset (forgot password) | 🟡 | всі | Laravel нативно (Breeze) / vanilla — `password_resets` таблиця + token | 1-3 г |
| 2FA (TOTP через Google Authenticator) ⭐ 🚫v | 🔴 | E | `pragmarx/google2fa-laravel` | 4-6 г |
| CAPTCHA на реєстрації | 🟡 | D | reCAPTCHA v2 (працює на vanilla теж) | 1-2 г |
| Login через Google OAuth ⭐ 🚫v | 🔴 | D | Laravel Socialite | 3-4 г |
| Session timeout | 🟡 | E | `config/session.php` + middleware | 0.5 г |
| Honeypot (anti-bot) | 🟡 | D | hidden field | 0.5 г |

---

## 15. Інтеграції (зовнішні API)

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| SMTP пошта (Mailtrap) | 🟡 | всі | `config/mail.php` | 1 г |
| Оплата Stripe test ⭐ | 🔴 | A | `stripe/stripe-php` | 6-10 г |
| Оплата LiqPay sandbox ⭐ | 🔴 | A | LiqPay SDK | 4-6 г |
| Google Maps / Leaflet | 🟡 | B, C | iframe або JS API | 2-3 г |
| Новова пошта (пошук відділень) ⭐ | 🔴 | A | Nova Poshta API | 4-6 г |
| Погода (OpenWeather) | 🟡 | C | HTTP::get() | 1-2 г |
| Курси валют (НБУ API) | 🟡 | A | HTTP::get() + cache | 1-2 г |
| SMS (TurboSMS, Vonage) | 🔴 | A, B | API + credentials | 3-4 г |
| Telegram-бот | 🔴 | A, B, D | Bot API webhook | 6-10 г |
| Google Calendar sync (бронювання) ⭐ | 🔴 | B | Google Calendar API | 8-12 г |
| Instagram embed (блог) | 🟡 | D | oEmbed | 1 г |

---

## 16. API та мобільне

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| REST API (Sanctum) ⭐ | 🟡 | всі | `routes/api.php` + Sanctum tokens | 4-6 г |
| API для мобільного app (всі CRUD) | 🔴 | всі | Resource controllers + API Resources | 6-10 г |
| Swagger/OpenAPI документація | 🔴 | всі | `darkaonline/l5-swagger` | 2-3 г |
| API rate limiting | 🟡 | всі | `throttle:60,1` | 0.5 г |
| Webhooks (зовнішні сповіщення) | 🔴 | E | queue + HTTP::post | 4-6 г |
| GraphQL endpoint | 🔴 | всі | `nuwave/lighthouse` | 8-12 г |

---

## 17. UX / якість UI

| Фіча | Рівень | Для типів | Техніка | Effort |
|------|--------|-----------|---------|--------|
| Responsive (mobile / tablet / desktop) | 🟢 | всі | Bootstrap grid / Tailwind | (must) |
| Dark mode ⭐ | 🟡 | D | CSS variables + toggle + localStorage | 2-3 г |
| Багатомовність (UK + EN) ⭐ | 🟡 | всі | `resources/lang/` + `__()` helper | 3-4 г |
| Keyboard shortcuts | 🔴 | E | Mousetrap.js | 2-3 г |
| Infinite scroll | 🟡 | D | AJAX + `?page=N` | 2-3 г |
| Skeleton loaders | 🟡 | всі | CSS animations під час fetch | 1-2 г |
| Toast повідомлення (замість alert) | 🟢 | всі | Bootstrap Toast / Notyf | 1 г |
| Breadcrumbs (хлібні крихти) | 🟡 | C, E | масив у layout | 0.5-1 г |
| Modal dialogs (замість нових сторінок) | 🟡 | E | Bootstrap Modal + AJAX | 2-3 г |
| Progressive Web App (PWA) ⭐ | 🔴 | D | manifest.json + service worker | 6-10 г |
| Offline mode | 🔴 | D | Service Worker cache | 6-10 г |
| Accessibility (WCAG AA) ⭐ | 🟡 | всі | labels, aria-*, contrast, keyboard nav | 2-4 г |
| Print stylesheet | 🟡 | C | `@media print` CSS | 1 г |

---

## 18. Wow-фактор (для захисту) ⭐

Набір функцій, які складно технічно, але **вражають на захисті**. Обирайте 1-2 для балів понад 95.

| Фіча | Для типів | Що вражає |
|------|-----------|-----------|
| Live-оновлення без reload (WebSocket / SSE) | будь-який | Нові коментарі / повідомлення з'являються миттєво |
| Геолокація користувача + ближчі об'єкти | B, C | «Найближча ветклініка до вас» |
| AI-рекомендації (колаборативна фільтрація) | A, D | «Вам сподобається X, тому що Y подобається» |
| Drag-and-drop kanban-дошка | E | Замовлення / задачі як картки |
| Експорт звіту з графіками у PDF | A, E | One-click PDF з Chart.js через headless Chrome |
| QR-код для швидкого доступу | B, C | QR на бронювання / експонат |
| Voice search | C | Web Speech API |
| Camera scan (barcode/QR) | A | getUserMedia + ZXing.js |
| AI-асистент у адмінці (ChatGPT для запитів) | E | «Скільки замовлень за вчора?» → SQL |
| Real-time collaboration (як Google Docs) | D | Y.js / Yjs + WebSocket |

---

## 19. Як вибрати свій набір

**Мінімальна версія (60-70 балів):**
- Усі 🟢 з [functionality-flow.md § Крок 5](functionality-flow.md#%D0%BA%D1%80%D0%BE%D0%BA-5) + ваш тип (3-8 пунктів у цьому каталозі)

**Середня версія (75-85 балів):**
- Мінімум + **3 фічі 🟡** з різних категорій
- Рекомендовано: 1 з «5. Пошук», 1 з «11. Адмін», 1 зі свого типу (3/7/8/9/10)

**Сильна версія (85-95 балів):**
- Мінімум + **5-7 🟡** + **1 🔴**
- Обов'язково 1 ⭐ з розділу «18. Wow-фактор»

**Максимальна версія (95+ балів):**
- Мінімум + **8+ 🟡** + **2-3 🔴** + **2 ⭐**
- Деплой на реальний домен (Heroku / Railway / DigitalOcean)
- Seed-демо з 50+ реалістичних записів
- Скриншоти у ПЗ з реальними користувачами-тест-акаунтами

---

## 20. Рекомендовані пресети за типами

### Shop (A) — мінімум для 75 балів
🟢 Кошик, статуси, CRUD товарів, категорії, пошук, пагінація, зображення, Checkout.
🟡 Зіркова оцінка, Відгуки тільки від покупців, Промокоди, Wish-list, Email на замовлення.

### Booking (B) — мінімум для 75 балів
🟢 Вибір слоту, перевірка зайнятості, статуси, CRUD послуг+майстрів, профіль з «мої бронювання».
🟡 FullCalendar, робочий графік, email-нагадування, оцінка після відвідування.

### Catalog (C) — мінімум для 75 балів
🟢 Детальна сторінка, пошук, мультифільтр, пагінація, категорії, зображення.
🟡 SEO-slug, пов'язані, timeline, favorites, Leaflet-карта (якщо музеї/бібліотеки).

### Community (D) — мінімум для 75 балів
🟢 Плоскі коментарі, лайки, CRUD постів, теги, пошук.
🟡 Polymorphic коментарі АБО треди, модерація, Markdown, аватари.
🔴 Follow-підписки АБО scheduled publish.

### Internal (E) — мінімум для 75 балів
🟢 Мінімум 2 ролі, Dashboard з лічильниками, мультифільтр, CRUD всіх сутностей.
🟡 Аудит-лог, Soft delete + Trash, експорт CSV, Chart.js графіки.
🔴 Роли через Spatie, 2FA, імперсонація.

---

## 21. Що НЕ брати як бонус (не варте зусиль)

| Фіча | Чому не варто |
|------|---------------|
| Блокчейн-інтеграція | 200+ годин, нерелевантно для курсової |
| Machine Learning (свій алгоритм) | Тема дипломної, не курсової |
| Повний клон Facebook | Scope занадто великий |
| Мобільний нативний app (Android/iOS) | Окрема технологія, не PHP |
| Custom CMS замість Laravel | Перевитрата часу на fundament |
| Мікросервіси | Надмірна архітектура для 1 розробника |

Якщо хочете справді великий проект — обговоріть з викладачем **перетворення курсової на дипломну**.

---

## Перехресні посилання

- [functionality-flow.md](functionality-flow.md) — визначення типу системи + обов'язкові блоки
- [system-design.md](system-design.md) — архітектура коду, структура Laravel
- [assignment.md](assignment.md) — темплейт вимог до курсової
- [Метод_реком_бекенд122.pdf](Метод_реком_бекенд122.pdf) — офіційна методичка
