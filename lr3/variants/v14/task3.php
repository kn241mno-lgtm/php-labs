<?php
/**
 * Завдання 3: Конструктор
 * Конструктор задає початкові значення title, director, year
 */

require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/Movie.php';

// Створюємо 3 об'єкти через конструктор
$movie1 = new Movie('Тіні забутих предків', 'Сергій Параджанов', 1965);
$movie2 = new Movie('Плем\'я', 'Мирослав Слабошпицький', 2014);
$movie3 = new Movie('Додому', 'Наріман Алієв', 2019);

$movies = [
    ['obj' => $movie1, 'avatar' => 'avatar-indigo', 'initial' => 'Т', 'var' => '$movie1'],
    ['obj' => $movie2, 'avatar' => 'avatar-green', 'initial' => 'П', 'var' => '$movie2'],
    ['obj' => $movie3, 'avatar' => 'avatar-amber', 'initial' => 'Д', 'var' => '$movie3'],
];

ob_start();
?>

<div class="task-header">
    <h1>Конструктор</h1>
    <p>Початкові значення задаються одразу при створенні об'єкта</p>
</div>

<div class="code-block">
<span class="code-comment">// Конструктор класу Movie</span>
<span class="code-keyword">public function</span> <span class="code-method">__construct</span>(<span class="code-class">string</span> <span class="code-variable">$title</span>, <span class="code-class">string</span> <span class="code-variable">$director</span>, <span class="code-class">int</span> <span class="code-variable">$year</span>) {
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">title</span> = <span class="code-variable">$title</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">director</span> = <span class="code-variable">$director</span>;
    <span class="code-variable">$this</span><span class="code-arrow">-></span><span class="code-method">year</span> = <span class="code-variable">$year</span>;
}

<span class="code-comment">// Створення через конструктор</span>
<span class="code-variable">$movie1</span> = <span class="code-keyword">new</span> <span class="code-class">Movie</span>(<span class="code-string">'Тіні забутих предків'</span>, <span class="code-string">'Сергій Параджанов'</span>, <span class="code-string">1965</span>);
<span class="code-variable">$movie2</span> = <span class="code-keyword">new</span> <span class="code-class">Movie</span>(<span class="code-string">'Плем\'я'</span>, <span class="code-string">'Мирослав Слабошпицький'</span>, <span class="code-string">2014</span>);
<span class="code-variable">$movie3</span> = <span class="code-keyword">new</span> <span class="code-class">Movie</span>(<span class="code-string">'Додому'</span>, <span class="code-string">'Наріман Алієв'</span>, <span class="code-string">2019</span>);
</div>

<div class="section-divider">
    <span class="section-divider-text">Об'єкти створені через конструктор</span>
</div>

<div class="users-grid">
<?php foreach ($movies as $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->title) ?></div>
                <div class="user-card-label"><?= $data['var'] ?> <span class="user-card-badge badge-constructor">constructor</span></div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">title</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->title) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">director</span>
                <span class="user-card-field-value"><?= htmlspecialchars($data['obj']->director) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">year</span>
                <span class="user-card-field-value"><?= $data['obj']->year ?></span>
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
        <?php foreach ($movies as $data): ?>
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
?>