<?php
/**
 * Завдання 1: Створення класів та об'єктів
 *
 * Варіант 30: клас Product, створення 3 об'єктів з довільними значеннями
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Product.php';

// Створюємо 3 об'єкти з довільними значеннями
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

$products = [
    ['obj' => $product1, 'avatar' => 'avatar-indigo', 'initial' => 'Н'],
    ['obj' => $product2, 'avatar' => 'avatar-green', 'initial' => 'К'],
    ['obj' => $product3, 'avatar' => 'avatar-amber', 'initial' => 'Р'],
];

ob_start();
?>

<div class="task-header">
    <h1>Створення об'єктів</h1>
    <p>Клас <code>Product</code> з властивостями: name, price, category</p>
</div>

<div class="code-block"><span class="code-comment">// Створюємо об'єкт та задаємо властивості</span>
<span class="code-variable">$product1</span> = <span class="code-keyword">new</span> <span class="code-class">Product</span>();
<span class="code-variable">$product1</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-string">'Ноутбук ASUS'</span>;
<span class="code-variable">$product1</span><span class="code-arrow">-></span><span class="code-method">price</span> = <span class="code-string">32500.00</span>;
<span class="code-variable">$product1</span><span class="code-arrow">-></span><span class="code-method">category</span> = <span class="code-string">'Електроніка'</span>;</div>

<div class="section-divider">
    <span class="section-divider-text">3 об'єкти</span>
</div>

<div class="users-grid">
    <?php foreach ($products as $i => $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->name) ?></div>
                <div class="user-card-label">Об'єкт #<?= $i + 1 ?></div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">name</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->name) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">price</span>
                <span class="user-card-field-value"><?= $data['obj']->price ?> грн</span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">category</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->category) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 1', 'task1-body');
