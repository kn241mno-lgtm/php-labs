<?php
/**
 * Завдання 4: Клонування об'єктів
 *
 * Демонстрація: __clone() задає значення за замовчанням при копіюванні
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Users.php';

// Оригінальний об'єкт (через конструктор)
$user3 = new Users('Дмитро', 'dmytro.dev', 'myP@ss789');

// Клонуємо — __clone() задає значення за замовчанням
$user4 = clone $user3;

ob_start();
?>

<div class="task-header">
    <h1>Клонування</h1>
    <p>Метод <code>__clone()</code> задає значення за замовчанням при копіюванні об'єкта</p>
</div>

<div class="code-block"><span class="code-comment">// Метод __clone() — викликається автоматично при clone</span>
<span class="code-keyword">public function</span> <span class="code-method">__clone</span>(): <span class="code-class">void</span>
{
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">name</span> = <span class="code-string">'User'</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">login</span> = <span class="code-string">'User'</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">password</span> = <span class="code-string">'qwerty'</span>;
}

<span class="code-comment">// Створюємо 4-й об'єкт через clone</span>
<span class="code-variable">$user4</span> = <span class="code-keyword">clone</span> <span class="code-variable">$user3</span>;</div>

<div class="section-divider">
    <span class="section-divider-text">Оригінал vs Клон</span>
</div>

<div class="comparison-wrapper">
    <div class="users-grid">
        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-amber">Д</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($user3->name) ?></div>
                    <div class="user-card-label">$user3 <span class="user-card-badge badge-constructor">original</span></div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">name</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user3->name) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">login</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user3->login) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">password</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user3->password) ?></span>
                </div>
            </div>
        </div>

        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-rose">U</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($user4->name) ?></div>
                    <div class="user-card-label">$user4 <span class="user-card-badge badge-clone">clone</span></div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">name</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user4->name) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">login</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user4->login) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">password</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($user4->password) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">getInfo() порівняння</span>
</div>

<div class="info-output">
    <div class="info-output-header">Результат getInfo() для оригіналу та клону</div>
    <div class="info-output-body">
        <div class="info-output-row">
            <span class="info-output-label">$user3</span>
            <span class="info-output-text"><?= htmlspecialchars($user3->getInfo()) ?></span>
        </div>
        <div class="info-output-row">
            <span class="info-output-label">$user4</span>
            <span class="info-output-text"><?= htmlspecialchars($user4->getInfo()) ?></span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Завдання 4', 'task4-body');
