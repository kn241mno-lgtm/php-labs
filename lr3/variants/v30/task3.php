<?php
/**
 * Завдання 3: Конструктор
 *
 * Варіант 30: конструктор задає початкові значення name, price, category
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Product.php';

// Створюємо 3 об'єкти через конструктор
$product1 = new Product('Ноутбук ASUS', 32500.00, 'Електроніка');
$product2 = new Product('Кросівки Nike', 4200.00, 'Взуття');
$product3 = new Product('Рюкзак Osprey', 6800.00, 'Аксесуари');

$products = [
    ['obj' => $product1, 'avatar' => 'avatar-indigo', 'initial' => 'Н', 'var' => '$product1'],
    ['obj' => $product2, 'avatar' => 'avatar-green', 'initial' => 'К', 'var' => '$product2'],
    ['obj' => $product3, 'avatar' => 'avatar-amber', 'initial' => 'Р', 'var' => '$product3'],
];

ob_start();
?>

<div class="task-header">
    <h1>Конструктор</h1>
    <p>Початкові значення задаються одразу при створенні об'єкта</p>
</div>

<div class="code-block"><span class="code-comment">// Конструктор класу Product</span>
<span class="code-keyword">public function</span> <span class="code-method">__construct</span>(<span class="code-class">string</span> <span class="code-variable">$name</span>, <span class="code-class">float</span> <span class="code-variable">$price</span>, <span class="code-class">string</span> <span class="code-variable">$category</span>)
{
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-variable">$name</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">price</span> = <span class="code-variable">$price</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">category</span> = <span class="code-variable">$category</span>;
}

<span class="code-comment">// Створення через конструктор</span>
<span class="code-variable">$product1</span> = <span class="code-keyword">new</span> <span class="code-class">Product</span>(<span class="code-string">'Ноутбук ASUS'</span>, <span class="code-string">32500.00</span>, <span class="code-string">'Електроніка'</span>);
<span class="code-variable">$product2</span> = <span class="code-keyword">new</span> <span class="code-class">Product</span>(<span class="code-string">'Кросівки Nike'</span>, <span class="code-string">4200.00</span>, <span class="code-string">'Взуття'</span>);
<span class="code-variable">$product3</span> = <span class="code-keyword">new</span> <span class="code-class">Product</span>(<span class="code-string">'Рюкзак Osprey'</span>, <span class="code-string">6800.00</span>, <span class="code-string">'Аксесуари'</span>);</div>

<div class="section-divider">
    <span class="section-divider-text">Об'єкти створені через конструктор</span>
</div>

<div class="users-grid">
    <?php foreach ($products as $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->name) ?></div>
                <div class="user-card-label"><?= $data['var'] ?> <span class="user-card-badge badge-constructor">constructor</span></div>
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

<div class="section-divider">
    <span class="section-divider-text">getInfo() для кожного</span>
</div>

<div class="info-output">
    <div class="info-output-header">Виклик getInfo() для об'єктів, створених через конструктор</div>
    <div class="info-output-body">
        <?php foreach ($products as $data): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $data['var'] ?></span>
            <span class="info-output-text"><?= htmlspecialchars($data['obj']->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 3', 'task3-body');
