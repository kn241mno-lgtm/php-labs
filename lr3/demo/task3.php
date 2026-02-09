<?php
/**
 * Завдання 3: Конструктор
 *
 * Демонстрація: конструктор задає початкові значення name, login, password
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Users.php';

// Створюємо 3 об'єкти через конструктор
$user1 = new Users('Олександр', 'oleksandr', 'pass123');
$user2 = new Users('Марія', 'maria_k', 'secure456');
$user3 = new Users('Дмитро', 'dmytro.dev', 'myP@ss789');

$users = [
    ['obj' => $user1, 'avatar' => 'avatar-indigo', 'initial' => 'О', 'var' => '$user1'],
    ['obj' => $user2, 'avatar' => 'avatar-green', 'initial' => 'М', 'var' => '$user2'],
    ['obj' => $user3, 'avatar' => 'avatar-amber', 'initial' => 'Д', 'var' => '$user3'],
];

ob_start();
?>

<div class="task-header">
    <h1>Конструктор</h1>
    <p>Початкові значення задаються одразу при створенні об'єкта</p>
</div>

<div class="code-block"><span class="code-comment">// Конструктор класу Users</span>
<span class="code-keyword">public function</span> <span class="code-method">__construct</span>(<span class="code-class">string</span> <span class="code-variable">$name</span>, <span class="code-class">string</span> <span class="code-variable">$login</span>, <span class="code-class">string</span> <span class="code-variable">$password</span>)
{
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-variable">$name</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">login</span> = <span class="code-variable">$login</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">password</span> = <span class="code-variable">$password</span>;
}

<span class="code-comment">// Створення через конструктор</span>
<span class="code-variable">$user1</span> = <span class="code-keyword">new</span> <span class="code-class">Users</span>(<span class="code-string">'Олександр'</span>, <span class="code-string">'oleksandr'</span>, <span class="code-string">'pass123'</span>);
<span class="code-variable">$user2</span> = <span class="code-keyword">new</span> <span class="code-class">Users</span>(<span class="code-string">'Марія'</span>, <span class="code-string">'maria_k'</span>, <span class="code-string">'secure456'</span>);
<span class="code-variable">$user3</span> = <span class="code-keyword">new</span> <span class="code-class">Users</span>(<span class="code-string">'Дмитро'</span>, <span class="code-string">'dmytro.dev'</span>, <span class="code-string">'myP@ss789'</span>);</div>

<div class="section-divider">
    <span class="section-divider-text">Об'єкти створені через конструктор</span>
</div>

<div class="users-grid">
    <?php foreach ($users as $data): ?>
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
                <span class="user-card-field-label">login</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->login) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">password</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->password) ?></span>
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
        <?php foreach ($users as $data): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $data['var'] ?></span>
            <span class="info-output-text"><?= htmlspecialchars($data['obj']->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 3', 'task3-body');
