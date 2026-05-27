# Шаблон `docs/routes.md` для курсової

> **Призначення:** дати студенту готову структуру документа з маршрутами, яку можна заповнити під свій проєкт.
> Цей файл не замінює `routes/web.php`, а пояснює архітектуру маршрутизації у ПЗ та README.

---

## 1. Як використовувати

1. Скопіюй структуру нижче у власний `docs/routes.md`.
2. Підстав свої URL, controller methods, middleware і ролі.
3. Не копіюй зайві розділи: залиш тільки те, що реально є в системі.

---

## 2. Мінімальний шаблон документа

```markdown
# Маршрути системи

## 1. Публічні маршрути

| URL | Method | Controller@method | Middleware | Призначення |
|-----|--------|-------------------|------------|-------------|
| / | GET | HomeController@index | — | Головна сторінка |
| /catalog | GET | CatalogController@index | — | Список записів |
| /catalog/{slug} | GET | CatalogController@show | — | Детальна сторінка |
| /login | GET/POST | AuthController@login | guest | Вхід |
| /register | GET/POST | AuthController@register | guest | Реєстрація |

## 2. Маршрути користувача

| URL | Method | Controller@method | Middleware | Призначення |
|-----|--------|-------------------|------------|-------------|
| /profile | GET | ProfileController@show | auth | Профіль |
| /profile/edit | GET/POST | ProfileController@update | auth | Редагування профілю |
| /orders | GET | OrderController@index | auth | Мої замовлення |
| /cart | GET | CartController@index | auth або session | Кошик |
| /checkout | POST | OrderController@store | auth | Оформлення |

## 3. Адмін-маршрути

| URL | Method | Controller@method | Middleware | Призначення |
|-----|--------|-------------------|------------|-------------|
| /admin | GET | Admin\\DashboardController@index | auth, admin | Dashboard |
| /admin/products | GET | Admin\\ProductController@index | auth, admin | Список товарів |
| /admin/products/create | GET/POST | Admin\\ProductController@store | auth, admin | Створення товару |
| /admin/products/{id}/edit | GET/POST | Admin\\ProductController@update | auth, admin | Редагування товару |
| /admin/orders | GET | Admin\\OrderController@index | auth, admin | Замовлення |

## 4. Middleware

| Middleware | Що перевіряє |
|------------|--------------|
| guest | Користувач неавторизований |
| auth | Користувач увійшов у систему |
| admin | Роль = admin |

## 5. Примітки

- Основний flow: гість → login/register → user action → admin moderation/status.
- Якщо це API-only, окремо описати `routes/api.php`.
```

---

## 3. Приклад для Laravel

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
});
```

Що перенести в `docs/routes.md`:

- URL
- HTTP method
- Controller@method
- middleware
- коротке пояснення бізнес-ролі маршруту

---

## 4. Приклад для vanilla PHP

```php
$router->get('/', 'HomeController@index');
$router->get('/catalog', 'CatalogController@index');
$router->get('/product', 'CatalogController@show');

$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');

$router->get('/profile', 'ProfileController@show', ['auth']);
$router->get('/admin/products', 'AdminProductController@index', ['auth', 'admin']);
$router->post('/admin/products/store', 'AdminProductController@store', ['auth', 'admin']);
```

У документації важливо показати не тільки route string, а й перевірки доступу.

---

## 5. Мінімум, який очікується на захисті

- [ ] Є публічні маршрути
- [ ] Є auth-маршрути
- [ ] Є user-маршрути
- [ ] Є admin-маршрути або окремий закритий блок
- [ ] Для кожного блоку зрозуміло, хто має доступ
- [ ] Маршрути відповідають реальному UI і кнопкам у системі

---

## 6. Пов'язані документи

- [system-design.md](system-design.md) — структура проекту і приклади `web.php`
- [assignment.md](assignment.md) — у ПЗ треба описати схему маршрутів
- [defense-checklist.md](defense-checklist.md) — на захисті часто питають про доступ до маршрутів