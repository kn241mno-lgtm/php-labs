<?php
/**
 * Завдання 1: Створення класів та об'єктів
 *
 * Демонстрація: клас Users, створення 3 об'єктів з довільними значеннями
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Users.php';

// Створюємо 3 об'єкти з довільними значеннями
$user1 = new Users();
$user1->name = 'Олександр';
$user1->login = 'oleksandr';
$user1->password = 'pass123';

$user2 = new Users();
$user2->name = 'Марія';
$user2->login = 'maria_k';
$user2->password = 'secure456';

$user3 = new Users();
$user3->name = 'Дмитро';
$user3->login = 'dmytro.dev';
$user3->password = 'myP@ss789';

$users = [
    ['obj' => $user1, 'avatar' => 'avatar-indigo', 'initial' => 'О'],
    ['obj' => $user2, 'avatar' => 'avatar-green', 'initial' => 'М'],
    ['obj' => $user3, 'avatar' => 'avatar-amber', 'initial' => 'Д'],
];

ob_start();
?>

<div class="task-header">
    <h1>Створення об'єктів</h1>
    <p>Клас <code>Users</code> з властивостями: name, login, password</p>
</div>

<div class="code-block"><span class="code-comment">// Створюємо об'єкт та задаємо властивості</span>
<span class="code-variable">$user1</span> = <span class="code-keyword">new</span> <span class="code-class">Users</span>();
<span class="code-variable">$user1</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-string">'Олександр'</span>;
<span class="code-variable">$user1</span><span class="code-arrow">-></span><span class="code-method">login</span> = <span class="code-string">'oleksandr'</span>;
<span class="code-variable">$user1</span><span class="code-arrow">-></span><span class="code-method">password</span> = <span class="code-string">'pass123'</span>;</div>

<div class="section-divider">
    <span class="section-divider-text">3 об'єкти</span>
</div>

<div class="users-grid">
    <?php foreach ($users as $i => $data): ?>
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

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 1', 'task1-body');
