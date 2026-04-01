<?php
/**
 * Завдання 1: Створення класів та об'єктів
 *
 * Варіант 14: клас Movie, створення 3 об'єктів
 */
require_once __DIR__ . '/layout.php';

// Опис класу 
class Movie {
    public $title;
    public $director;
    public $year;
}

// об'єкти
$movie1 = new Movie();
$movie1->title = 'Тіні забутих предків';
$movie1->director = 'Сергій Параджанов';
$movie1->year = 1965;

$movie2 = new Movie();
$movie2->title = 'Плем\'я';
$movie2->director = 'Мирослав Слабошпицький';
$movie2->year = 2014;

$movie3 = new Movie();
$movie3->title = 'Атлантида';
$movie3->director = 'Валентин Васянович';
$movie3->year = 2019;

$movies = [
    ['obj' => $movie1, 'avatar' => 'avatar-indigo', 'initial' => 'Т'],
    ['obj' => $movie2, 'avatar' => 'avatar-green', 'initial' => 'П'],
    ['obj' => $movie3, 'avatar' => 'avatar-amber', 'initial' => 'А'],
];

ob_start();
?>

<div class="task-header">
    <h1>Створення об'єктів</h1>
    <p>Клас <code>Movie</code> з властивостями: title, director, year</p>
</div>

<div class="section-divider">
    <span class="section-divider-text">3 об'єкти</span>
</div>

<div class="users-grid">
    <?php foreach ($movies as $i => $data): ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $data['avatar'] ?>"><?= $data['initial'] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($data['obj']->title) ?></div>
                <div class="user-card-label">Об'єкт #<?= $i + 1 ?></div>
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

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 1', 'task1-body');