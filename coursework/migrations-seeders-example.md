# Міграції + Seeders на прикладі v1 Книгарня

> **Призначення:** показати організацію schema-as-code через міграції для **обох стеків**:
> 1. **Vanilla PHP** — структура `sql/migrations/` + простий runner.
> 2. **Laravel** — нативні migrations + Seeders + Factories.
>
> Використовується **повна** схема з [schemas.md](schemas.md#4-e-commerce--еталон-v1-книгарня).

## Зміст

1. [Навіщо міграції](#1-навіщо-міграції)
2. [Vanilla PHP підхід](#2-vanilla-php-підхід)
3. [Laravel migrations](#3-laravel-migrations)
4. [Laravel Seeders](#4-laravel-seeders)
5. [Laravel Factories](#5-laravel-factories)
6. [Типові помилки](#6-типові-помилки)

---

## 1. Навіщо міграції

**Problem:** один `.sql` файл з усіма CREATE TABLE — вночі через тиждень треба додати поле → треба:
- Знайти поточний стан production БД
- Додати ALTER TABLE руками
- Оновити `.sql` файл
- Не забути зробити це на dev/staging/prod
- Помилка? — втрата даних

**Solution:** міграції
- Кожна зміна = окремий файл з timestamp
- Файли виконуються по черзі
- БД знає які міграції вже застосовані (табличка `migrations`)
- Rollback = виконати зворотну операцію

---

## 2. Vanilla PHP підхід

### Структура проекту

```
project/
├── config/
│   └── database.php             # конфіг підключення
├── sql/
│   ├── migrations/
│   │   ├── 2026_01_15_100000_create_users_table.sql
│   │   ├── 2026_01_15_100100_create_roles_table.sql
│   │   ├── 2026_01_15_100200_create_user_roles_table.sql
│   │   ├── 2026_01_15_100300_create_addresses_table.sql
│   │   ├── 2026_01_15_100400_create_categories_table.sql
│   │   ├── 2026_01_15_100500_create_publishers_table.sql
│   │   ├── 2026_01_15_100600_create_authors_table.sql
│   │   ├── 2026_01_15_100700_create_books_table.sql
│   │   ├── 2026_01_15_100800_create_book_authors_table.sql
│   │   ├── 2026_01_15_100900_create_book_images_table.sql
│   │   ├── 2026_01_15_101000_create_cart_items_table.sql
│   │   ├── 2026_01_15_101100_create_coupons_table.sql
│   │   ├── 2026_01_15_101200_create_orders_table.sql
│   │   ├── 2026_01_15_101300_create_order_items_table.sql
│   │   ├── 2026_01_15_101400_create_payments_table.sql
│   │   ├── 2026_01_15_101500_create_reviews_table.sql
│   │   ├── 2026_01_15_101600_create_wishlist_table.sql
│   │   ├── 2026_01_15_101700_create_audit_log_table.sql
│   │   └── 2026_01_15_101800_create_password_resets_table.sql
│   └── seeders/
│       ├── 01_roles_seeder.sql
│       ├── 02_categories_seeder.sql
│       ├── 03_publishers_seeder.sql
│       ├── 04_authors_seeder.sql
│       ├── 05_books_seeder.sql
│       └── 06_sample_users_seeder.sql
└── scripts/
    ├── migrate.php              # runner для migrations
    └── seed.php                 # runner для seeders
```

### Приклад міграції — create_users_table.sql

```sql
-- Migration: create_users_table
-- Created: 2026-01-15
-- Description: Initial users table with soft delete + bcrypt password

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL COMMENT 'bcrypt, NEVER plaintext',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    birth_date DATE NULL,
    email_verified_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Приклад міграції з FK — create_orders_table.sql

```sql
-- Migration: create_orders_table
-- Created: 2026-01-15
-- Depends: users, addresses, coupons

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    address_id INT NOT NULL,
    coupon_id INT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    current_status ENUM(
        'pending', 'paid', 'processing', 'shipped',
        'delivered', 'cancelled', 'refunded'
    ) NOT NULL DEFAULT 'pending',
    subtotal_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL,
    INDEX idx_user_status (user_id, current_status),
    INDEX idx_created (created_at DESC),
    INDEX idx_order_number (order_number),
    CHECK (subtotal_amount >= 0),
    CHECK (total_amount >= 0),
    CHECK (discount_amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Migration Runner — scripts/migrate.php

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../config/database.php';

function runMigrations(PDO $pdo, string $migrationsDir): void {
    // 1. Створити таблицю migrations якщо не існує
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT PRIMARY KEY AUTO_INCREMENT,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 2. Отримати список вже виконаних
    $applied = $pdo->query("SELECT filename FROM migrations")
        ->fetchAll(PDO::FETCH_COLUMN);

    // 3. Отримати список файлів з папки (відсортований)
    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    // 4. Застосувати нові
    foreach ($files as $file) {
        $filename = basename($file);

        if (in_array($filename, $applied, true)) {
            echo "SKIP: {$filename} (already applied)\n";
            continue;
        }

        $sql = file_get_contents($file);

        try {
            $pdo->beginTransaction();
            $pdo->exec($sql);
            $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)")
                ->execute([$filename]);
            $pdo->commit();
            echo "✅ APPLIED: {$filename}\n";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "❌ FAILED: {$filename}\n";
            echo "   Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

runMigrations($pdo, __DIR__ . '/../sql/migrations');
echo "Done!\n";
```

**Запуск:**
```bash
php scripts/migrate.php
```

### Seeder Runner — scripts/seed.php

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../config/database.php';

function runSeeders(PDO $pdo, string $seedersDir): void {
    $files = glob($seedersDir . '/*.sql');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        $sql = file_get_contents($file);

        try {
            $pdo->beginTransaction();
            $pdo->exec($sql);
            $pdo->commit();
            echo "✅ SEEDED: {$filename}\n";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "❌ FAILED: {$filename}\n";
            echo "   Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

$pdo = new PDO(/* ... */);
runSeeders($pdo, __DIR__ . '/../sql/seeders');
echo "Seed completed!\n";
```

### Приклад seeder — 01_roles_seeder.sql

```sql
-- Seeder: roles
-- Description: Basic RBAC roles

INSERT INTO roles (name, description) VALUES
    ('admin', 'Повний доступ до системи'),
    ('manager', 'Управління контентом і замовленнями'),
    ('customer', 'Звичайний користувач — купівля книг'),
    ('author', 'Автор книг (якщо є можливість додавати свої)')
ON DUPLICATE KEY UPDATE description = VALUES(description);
```

### Приклад seeder — 02_categories_seeder.sql

```sql
-- Seeder: categories
INSERT INTO categories (name, slug, parent_id) VALUES
    ('Художня література', 'fiction', NULL),
    ('Наукова література', 'science', NULL),
    ('Дитячі книги', 'kids', NULL),
    ('Бізнес і фінанси', 'business', NULL);

-- Підкатегорії (використовуємо id з попереднього INSERT)
INSERT INTO categories (name, slug, parent_id)
SELECT 'Роман', 'novel', id FROM categories WHERE slug = 'fiction';

INSERT INTO categories (name, slug, parent_id)
SELECT 'Детектив', 'detective', id FROM categories WHERE slug = 'fiction';

INSERT INTO categories (name, slug, parent_id)
SELECT 'Фентезі', 'fantasy', id FROM categories WHERE slug = 'fiction';

INSERT INTO categories (name, slug, parent_id)
SELECT 'Психологія', 'psychology', id FROM categories WHERE slug = 'science';

INSERT INTO categories (name, slug, parent_id)
SELECT 'Історія', 'history', id FROM categories WHERE slug = 'science';
```

### Приклад seeder — 06_sample_users_seeder.sql

```sql
-- Seeder: sample users (для dev environment тільки)
-- Паролі згенеровані через password_hash('password123', PASSWORD_BCRYPT)

INSERT INTO users (email, password_hash, first_name, last_name, phone) VALUES
    ('admin@example.com',    '$2y$10$abc...(60 chars)', 'Admin',  'User',   '+380501111111'),
    ('manager@example.com',  '$2y$10$def...(60 chars)', 'Manager','User',   '+380502222222'),
    ('customer1@example.com','$2y$10$ghi...(60 chars)', 'Ivan',   'Petrov', '+380503333333'),
    ('customer2@example.com','$2y$10$jkl...(60 chars)', 'Olga',   'Ivanova','+380504444444');

-- Призначення ролей
INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id FROM users u CROSS JOIN roles r
WHERE u.email = 'admin@example.com' AND r.name = 'admin';

INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id FROM users u CROSS JOIN roles r
WHERE u.email = 'manager@example.com' AND r.name = 'manager';

INSERT INTO user_roles (user_id, role_id)
SELECT u.id, r.id FROM users u CROSS JOIN roles r
WHERE u.email IN ('customer1@example.com', 'customer2@example.com')
  AND r.name = 'customer';
```

**⚠️ Важливо:** seed-паролі **тільки для dev**. На production — видалити seeder або встановити перевірку `APP_ENV=dev`.

---

## 3. Laravel migrations

### Створення міграції

```bash
php artisan make:migration create_books_table
# створює: database/migrations/2026_01_15_120000_create_books_table.php
```

### Приклад — Books migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('restrict');
            $table->foreignId('publisher_id')
                ->nullable()
                ->constrained('publishers')
                ->onDelete('set null');

            $table->string('isbn', 20)->unique();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('year')->nullable();
            $table->integer('pages')->nullable();
            $table->decimal('rating', 3, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category_id', 'deleted_at']);
            $table->index('created_at');
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('books');
    }
};
```

### Приклад — Orders migration з CHECK і ENUM

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('address_id')->constrained()->onDelete('restrict');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');

            $table->string('order_number', 50)->unique();
            $table->enum('current_status', [
                'pending', 'paid', 'processing',
                'shipped', 'delivered', 'cancelled', 'refunded'
            ])->default('pending');

            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'current_status']);
            $table->index('created_at');
        });

        // CHECK constraints (Laravel не підтримує нативно — через raw SQL)
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_subtotal_positive CHECK (subtotal_amount >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_total_positive CHECK (total_amount >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_discount_positive CHECK (discount_amount >= 0)');
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
```

### Приклад — Junction table (book_authors)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('book_authors', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('restrict');
            $table->integer('author_order')->default(1);

            // Composite PK — НЕ id
            $table->primary(['book_id', 'author_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('book_authors');
    }
};
```

### Приклад — Tickets з composite UNIQUE (race condition)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('cinema_sessions')->onDelete('cascade');
            $table->foreignId('seat_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['reserved', 'paid', 'cancelled', 'used'])->default('reserved');
            $table->string('booking_code', 50)->unique();
            $table->timestamps();

            // ⚠️ Критично — запобігає race condition
            $table->unique(['session_id', 'seat_id'], 'uq_session_seat');

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
```

### Running migrations

```bash
# Нова встановка
php artisan migrate

# Відкат останньої
php artisan migrate:rollback

# Відкат всіх + наново
php artisan migrate:fresh

# Fresh + seed
php artisan migrate:fresh --seed

# Статус
php artisan migrate:status
```

---

## 4. Laravel Seeders

### Створення seeder

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder CategorySeeder
php artisan make:seeder DatabaseSeeder  # головний
```

### RoleSeeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder {
    public function run(): void {
        $roles = [
            ['name' => 'admin',    'description' => 'Повний доступ'],
            ['name' => 'manager',  'description' => 'Управління контентом'],
            ['name' => 'customer', 'description' => 'Звичайний користувач'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
```

### CategorySeeder (з nested)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder {
    public function run(): void {
        // Корневі категорії
        $fiction = Category::create([
            'name' => 'Художня література',
            'slug' => 'fiction',
            'parent_id' => null,
        ]);

        $science = Category::create([
            'name' => 'Наукова література',
            'slug' => 'science',
            'parent_id' => null,
        ]);

        // Підкатегорії
        Category::create(['name' => 'Роман',      'slug' => 'novel',     'parent_id' => $fiction->id]);
        Category::create(['name' => 'Детектив',   'slug' => 'detective', 'parent_id' => $fiction->id]);
        Category::create(['name' => 'Фентезі',    'slug' => 'fantasy',   'parent_id' => $fiction->id]);
        Category::create(['name' => 'Психологія', 'slug' => 'psychology','parent_id' => $science->id]);
        Category::create(['name' => 'Історія',    'slug' => 'history',   'parent_id' => $science->id]);
    }
}
```

### DatabaseSeeder (orchestrator)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Порядок важливий — спочатку довідники, потім дані з FK
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            PublisherSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
            UserSeeder::class,  // з ролями
        ]);

        // Фабрика для генерації тестових даних
        if (app()->environment(['local', 'dev'])) {
            \App\Models\User::factory(50)->create();
            \App\Models\Book::factory(200)->create();
        }
    }
}
```

---

## 5. Laravel Factories

### Створення factory

```bash
php artisan make:factory UserFactory --model=User
php artisan make:factory BookFactory --model=Book
```

### UserFactory

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => Hash::make('password123'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => '+38050' . $this->faker->numerify('#######'),
            'birth_date' => $this->faker->date('Y-m-d', '2005-01-01'),
            'email_verified_at' => now(),
        ];
    }

    public function admin(): static {
        return $this->afterCreating(function (User $user) {
            $user->roles()->attach(\App\Models\Role::where('name', 'admin')->first());
        });
    }

    public function customer(): static {
        return $this->afterCreating(function (User $user) {
            $user->roles()->attach(\App\Models\Role::where('name', 'customer')->first());
        });
    }
}
```

### BookFactory

```php
<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory {
    protected $model = Book::class;

    public function definition(): array {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'publisher_id' => Publisher::inRandomOrder()->first()?->id,
            'isbn' => $this->faker->unique()->isbn13(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 50, 2000),
            'stock' => $this->faker->numberBetween(0, 100),
            'year' => $this->faker->year(),
            'pages' => $this->faker->numberBetween(100, 800),
            'rating' => $this->faker->randomFloat(2, 1, 5),
        ];
    }

    public function configure(): static {
        return $this->afterCreating(function (Book $book) {
            // 1-3 авторів на книгу
            $authors = Author::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($authors as $index => $author) {
                $book->authors()->attach($author->id, ['author_order' => $index + 1]);
            }
        });
    }
}
```

### Використання

```php
// В seeder
User::factory()->admin()->create(['email' => 'admin@example.com']);
User::factory(50)->customer()->create();
Book::factory(200)->create();

// У тестах
$user = User::factory()->create();
$book = Book::factory()->create(['price' => 199.99]);
```

---

## 6. Типові помилки

### 6.1. Неправильний порядок міграцій (FK на неіснуючу таблицю)

❌ Погано:
```
2026_01_15_100000_create_orders_table.sql
2026_01_15_100100_create_users_table.sql
```

Міграція orders виконується першою і падає (`users` не існує).

✅ Правильно — залежні таблиці **після** того від чого залежать:
```
2026_01_15_100000_create_users_table.sql
2026_01_15_100100_create_addresses_table.sql
2026_01_15_100200_create_orders_table.sql
```

### 6.2. Редагування вже застосованих міграцій

❌ Погано: відкрити файл `2026_01_15_100000_create_users.sql` який вже виконано на production, додати туди нове поле.

Production БД вже має схему за старою версією цього файлу. Зміна файлу не призведе до зміни production.

✅ Правильно: створити **нову** міграцію:
```bash
php artisan make:migration add_birth_date_to_users_table
```

```php
public function up(): void {
    Schema::table('users', function (Blueprint $table) {
        $table->date('birth_date')->nullable()->after('phone');
    });
}
```

### 6.3. Seed виконує INSERT без `IF NOT EXISTS` / `ON DUPLICATE KEY`

❌ Погано:
```sql
INSERT INTO roles (name) VALUES ('admin');
-- другий запуск → Duplicate entry error
```

✅ Правильно:
```sql
INSERT INTO roles (name) VALUES ('admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);
```

Або в Laravel:
```php
Role::updateOrCreate(['name' => 'admin'], ['description' => 'Full access']);
```

### 6.4. Seeder з справжніми даними

❌ Погано:
```sql
INSERT INTO users VALUES (1, 'ivan.petrov@gmail.com', 'Іван Петров', '+380501234567');
```

Реальна людина → GDPR порушення.

✅ Правильно: Faker, email `@example.com`, phone `+38000...`, password `password123`.

### 6.5. Відсутність `down()` (rollback)

❌ Погано:
```php
public function up(): void {
    Schema::create('books', ...);
}

public function down(): void {
    // нічого — неможливо відкотити
}
```

✅ Правильно:
```php
public function down(): void {
    Schema::dropIfExists('books');
}
```

### 6.6. Транзакційні обмеження

DDL (CREATE TABLE, ALTER TABLE) в MySQL — **не транзакційні** → якщо в середині міграції помилка, частина вже застосована. Laravel має вбудований механізм `DB::transaction()` але для DDL він не допоможе.

✅ Стратегія: **маленькі атомарні міграції** — одна = одна операція.

---

## Підсумок

**Мінімум для ЛР4 (оцінка 3-4):**
- `.sql` файл з усіма CREATE TABLE
- Порядок створення правильний

**Нормально для ЛР4 (оцінка 4-5):**
- Міграції як окремі файли з timestamp
- Прості seeders з довідковими даними

**Ідеально для курсової (оцінка 5):**
- Laravel migrations з `up()` + `down()`
- Seeders + Factories для генерації тестових даних
- Різні seeders для dev/staging/prod
- `DatabaseSeeder` як orchestrator

## Посилання

- Повні схеми: [schemas.md](schemas.md)
- ER-діаграми: [er-diagrams.md](er-diagrams.md)
- Чекліст перед здачею: [checklist-lr4.md](checklist-lr4.md)
- Типові помилки: [typical-mistakes.md](typical-mistakes.md)
- Приклади запитів: [example-queries.md](example-queries.md)
