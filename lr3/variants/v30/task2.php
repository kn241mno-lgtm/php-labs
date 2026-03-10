<?php
/**
 * Завдання 2: Метод getInfo()
 *
 * Варіант 30: метод об'єкта Product, що виводить значення властивостей
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Product.php';

// Створюємо 3 об'єкти
$product1 = new Product();
$product1->name = 'Ноутбук ASUS';
$product1->price = 32500.00;
$product1->category = 'Електроніка';

$product2 = new Product();
$product2->name = 'Кросівки Nike';
$product2->price = 4200.00;
$product2->category = 'Взуття';

$product3 = new Product();
$product3->name = 'Рюкзак Osprey';
$product3->price = 6800.00;
$product3->category = 'Аксесуари';

$products = [$product1, $product2, $product3];
$labels = ['$product1', '$product2', '$product3'];

ob_start();
?>

<div class="task-header">
    <h1>Метод getInfo()</h1>
    <p>Виводить значення властивостей об'єкта</p>
</div>

<div class="code-block"><span class="code-comment">// Метод getInfo() повертає рядок з інформацією</span>
<span class="code-keyword">public function</span> <span class="code-method">getInfo</span>(): <span class="code-class">string</span>
{
    <span class="code-keyword">return</span> <span class="code-string">"Товар: {$this->name}, Ціна: {$this->price} грн, Категорія: {$this->category}"</span>;
}

<span class="code-comment">// Виклик для кожного об'єкта</span>
<span class="code-variable">$product1</span><span class="code-arrow">-></span><span class="code-method">getInfo</span>();</div>

<div class="section-divider">
    <span class="section-divider-text">Результат виклику</span>
</div>

<div class="info-output">
    <div class="info-output-header">getInfo() — вивід для кожного об'єкта</div>
    <div class="info-output-body">
        <?php foreach ($products as $i => $product): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $labels[$i] ?></span>
            <span class="info-output-text"><?= htmlspecialchars($product->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">Картки товарів</span>
</div>

<div class="users-grid">
    <?php
    $avatars = ['avatar-indigo', 'avatar-green', 'avatar-amber'];
    $initials = ['Н', 'К', 'Р'];
    foreach ($products as $i => $product):
    ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $avatars[$i] ?>"><?= $initials[$i] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($product->name) ?></div>
                <div class="user-card-label"><?= $labels[$i] ?>->getInfo()</div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">name</span>
                <span class="user-card-field-value"><?= htmlspecialchars($product->name) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">price</span>
                <span class="user-card-field-value"><?= $product->price ?> грн</span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">category</span>
                <span class="user-card-field-value"><?= htmlspecialchars($product->category) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 2', 'task2-body');
