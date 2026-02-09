<?php
/**
 * Завдання 2: Метод getInfo()
 *
 * Демонстрація: метод об'єкта, що виводить значення властивостей
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Users.php';

// Створюємо 3 об'єкти
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

$users = [$user1, $user2, $user3];
$labels = ['$user1', '$user2', '$user3'];

ob_start();
?>

<div class="task-header">
    <h1>Метод getInfo()</h1>
    <p>Виводить значення властивостей об'єкта</p>
</div>

<div class="code-block"><span class="code-comment">// Метод getInfo() повертає рядок з інформацією</span>
<span class="code-keyword">public function</span> <span class="code-method">getInfo</span>(): <span class="code-class">string</span>
{
    <span class="code-keyword">return</span> <span class="code-string">"Ім'я: {$this->name}, Логін: {$this->login}, Пароль: {$this->password}"</span>;
}

<span class="code-comment">// Виклик для кожного об'єкта</span>
<span class="code-variable">$user1</span><span class="code-arrow">-></span><span class="code-method">getInfo</span>();</div>

<div class="section-divider">
    <span class="section-divider-text">Результат виклику</span>
</div>

<div class="info-output">
    <div class="info-output-header">getInfo() — вивід для кожного об'єкта</div>
    <div class="info-output-body">
        <?php foreach ($users as $i => $user): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $labels[$i] ?></span>
            <span class="info-output-text"><?= htmlspecialchars($user->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">Картки користувачів</span>
</div>

<div class="users-grid">
    <?php
    $avatars = ['avatar-indigo', 'avatar-green', 'avatar-amber'];
    $initials = ['О', 'М', 'Д'];
    foreach ($users as $i => $user):
    ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $avatars[$i] ?>"><?= $initials[$i] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($user->name) ?></div>
                <div class="user-card-label"><?= $labels[$i] ?>->getInfo()</div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">name</span>
                <span class="user-card-field-value"><?= htmlspecialchars($user->name) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">login</span>
                <span class="user-card-field-value"><?= htmlspecialchars($user->login) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">password</span>
                <span class="user-card-field-value"><?= htmlspecialchars($user->password) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 2', 'task2-body');
