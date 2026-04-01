<?php
/**
 * Завдання 4: Клонування об'єктів
 *
 * Варіант 14: __clone() — дозволяє точне клонування об'єкта з усіма властивостями
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Movie.php';

// Оригінальний об'єкт
$movie3 = new Movie('Атлантида', 'Валентин Васянович', 2019);

// Клонування
$movie4 = clone $movie3;

ob_start();
?>

<div class="task-header">
    <h1>Клонування</h1>
    <p>Метод <code>__clone()</code> дозволяє точне клонування об'єкта з усіма властивостями</p>
</div>  

<div class="code-block">
<span class="code-comment">// Метод __clone() - точне клонування</span>
<span class="code-keyword">public function</span> <span class="code-method">__clone</span>(): <span class="code-class">void</span>
{
    <span class="code-comment">// Властивості копіюються автоматично</span>
}

<span class="code-comment">// Створюємо 4-й об'єкт через clone</span>
<span class="code-variable">$movie4</span> = <span class="code-keyword">clone</span> <span class="code-variable">$movie3</span>;
</div>

<div class="section-divider">
    <span class="section-divider-text">Оригінал vs Клон</span>
</div>

<div class="comparison-wrapper">
    <div class="users-grid">
        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-amber">М</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($movie3->title) ?></div>
                    <div class="user-card-label">$movie3</div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">title</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($movie3->title) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">director</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($movie3->director) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">year</span>
                    <span class="user-card-field-value"><?= $movie3->year ?></span>
                </div>
            </div>
        </div>

        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-avatar avatar-rose">К</div>
                <div>
                    <div class="user-card-name"><?= htmlspecialchars($movie4->title) ?></div>
                    <div class="user-card-label">$movie4</div>
                </div>
            </div>
            <div class="user-card-body">
                <div class="user-card-field">
                    <span class="user-card-field-label">title</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($movie4->title) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">director</span>
                    <span class="user-card-field-value"><?= htmlspecialchars($movie4->director) ?></span>
                </div>
                <div class="user-card-field">
                    <span class="user-card-field-label">year</span>
                    <span class="user-card-field-value"><?= $movie4->year ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">getInfo()</span>
</div>

<div class="info-output">
    <div class="info-output-body">
        <div class="info-output-row">
            <span class="info-output-label">$movie3</span>
            <span class="info-output-text"><?= htmlspecialchars($movie3->getInfo()) ?></span>
        </div>
        <div class="info-output-row">
            <span class="info-output-label">$movie4</span>
            <span class="info-output-text"><?= htmlspecialchars($movie4->getInfo()) ?></span>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 4', 'task4-body');
?>