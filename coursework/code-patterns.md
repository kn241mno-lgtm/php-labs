# Типові патерни коду для курсової

> **Призначення:** дати короткі робочі шаблони для трьох місць, на яких студенти спотикаються найчастіше: кошик, авторизація доступу, валідація.
> Це не готовий проєкт, а стартові патерни, які треба адаптувати під свою тему.

---

## 1. Cart: session для гостя + БД для user

### Laravel: session cart для гостя

```php
public function add(Product $product): \Illuminate\Http\RedirectResponse
{
    $cart = session()->get('cart', []);

    if (isset($cart[$product->id])) {
        $cart[$product->id]['quantity']++;
    } else {
        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'image' => $product->image,
        ];
    }

    session()->put('cart', $cart);

    return back()->with('success', 'Товар додано до кошика.');
}
```

### Laravel: перенос session cart у БД після логіну

```php
public function syncSessionCartToDatabase(User $user): void
{
    $cart = session()->get('cart', []);

    foreach ($cart as $item) {
        CartItem::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $item['id'],
            ],
            [
                'quantity' => \DB::raw('quantity + ' . (int) $item['quantity']),
            ]
        );
    }

    session()->forget('cart');
}
```

> Якщо не хочеш `DB::raw`, спочатку дістань запис, потім додай кількість окремим `save()`.

### Vanilla PHP: session cart

```php
public function addToCart(): void
{
    $productId = (int) ($_POST['product_id'] ?? 0);
    $product = Product::find($productId);

    if (!$product) {
        $_SESSION['flash_error'] = 'Товар не знайдено.';
        $this->redirect('?route=catalog');
    }

    $_SESSION['cart'] ??= [];

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity']++;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
        ];
    }

    $_SESSION['flash_success'] = 'Товар додано до кошика.';
    $this->redirect('?route=cart');
}
```

### На що звернути увагу

- Session cart добре підходить для гостя.
- Для авторизованого user кошик краще зберігати в БД.
- У `order_items` зберігайте `price_at_moment`, а не тільки `product_id`, інакше історія замовлення зламається після зміни ціни.

---

## 2. Policy / перевірка доступу

### Laravel: Policy для редагування свого поста

```php
class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }
}
```

```php
public function edit(Post $post)
{
    $this->authorize('update', $post);

    return view('posts.edit', compact('post'));
}
```

### Laravel: middleware для admin-зони

```php
class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
```

### Vanilla PHP: мінімальна перевірка ролі

```php
protected function requireRole(string $role): void
{
    $user = $_SESSION['user'] ?? null;

    if (!$user || ($user['role'] ?? null) !== $role) {
        http_response_code(403);
        require __DIR__ . '/../views/errors/403.php';
        exit;
    }
}
```

> Не перевіряйте права через `if ($_SESSION['user']['id'] == 1)`.
> Роль має бути явною: `admin`, `manager`, `user`.

---

## 3. FormRequest / server-side validation

### Laravel: окремий request-клас

```php
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Вкажіть назву.',
            'price.required' => 'Вкажіть ціну.',
            'price.numeric' => 'Ціна має бути числом.',
            'category_id.exists' => 'Оберіть існуючу категорію.',
            'image.image' => 'Файл має бути зображенням.',
        ];
    }
}
```

```php
public function store(StoreProductRequest $request): \Illuminate\Http\RedirectResponse
{
    $data = $request->validated();

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    Product::create($data);

    return redirect()->route('admin.products.index')
        ->with('success', 'Товар створено.');
}
```

### Vanilla PHP: ручна валідація в одному місці

```php
public function validateProduct(array $input): array
{
    $errors = [];

    if (trim($input['name'] ?? '') === '') {
        $errors['name'] = 'Вкажіть назву.';
    }

    if (!isset($input['price']) || !is_numeric($input['price']) || (float) $input['price'] < 0) {
        $errors['price'] = 'Ціна має бути невід' . "'" . 'ємним числом.';
    }

    return $errors;
}
```

### На що звернути увагу

- HTML `required` не заміняє server-side validation.
- Повідомлення про помилки мають бути зрозумілі студенту/користувачу.
- Валідація не повинна розмазуватися по 5 контролерах. Зберіть її в `FormRequest` або окремий валідатор.

---

## 4. Пов'язані документи

- [system-design.md](system-design.md) — куди ці патерни вбудовуються в архітектурі
- [feature-catalog.md](feature-catalog.md) — які модулі обов'язкові, а які бонусні
- [functionality-flow.md](functionality-flow.md) — який саме flow треба реалізувати для вашого типу системи