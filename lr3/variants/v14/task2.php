<?php
/**
 * Завдання 2: Метод getInfo()
 *
 * Варіант 14: метод об'єкта Product, що виводить значення властивостей
 */
require_once __DIR__ . '/layout.php';

// --- Клас Movie ---
class Movie {
    public $title;
    public $director;
    public $year;

    // Метод getInfo() повертає рядок з інформацією про фільм
    public function getInfo(): string
    {
        return "Фільм: {$this->title}, Режисер: {$this->director}, Рік: {$this->year}";
    }
}

// --- Створюємо 3 об'єкти Movie ---
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

$movies = [$movie1, $movie2, $movie3];
$labels = ['$movie1', '$movie2', '$movie3'];

ob_start();
?>

<div class="task-header">
    <h1>Метод getInfo()</h1>
    <p>Виводить інформацію про фільм</p>
</div>

<div class="info-output">
    <div class="info-output-header">getInfo() — вивід для кожного об'єкта</div>
    <div class="info-output-body">
        <?php foreach ($movies as $i => $movie): ?>
        <div class="info-output-row">
            <span class="info-output-label"><?= $labels[$i] ?></span>
            <span class="info-output-text"><?= htmlspecialchars($movie->getInfo()) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="section-divider">
    <span class="section-divider-text">Картки фільмів</span>
</div>

<div class="users-grid">
    <?php
    $avatars = ['avatar-indigo', 'avatar-green', 'avatar-amber'];
    $initials = ['Т', 'П', 'А']; // перші літери назв фільмів
    foreach ($movies as $i => $movie):
    ?>
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-card-avatar <?= $avatars[$i] ?>"><?= $initials[$i] ?></div>
            <div>
                <div class="user-card-name"><?= htmlspecialchars($movie->title) ?></div>
                <div class="user-card-label"><?= $labels[$i] ?>->getInfo()</div>
            </div>
        </div>
        <div class="user-card-body">
            <div class="user-card-field">
                <span class="user-card-field-label">title</span>
                <span class="user-card-field-value"><?= htmlspecialchars($movie->title) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">director</span>
                <span class="user-card-field-value"><?= htmlspecialchars($movie->director) ?></span>
            </div>
            <div class="user-card-field">
                <span class="user-card-field-label">year</span>
                <span class="user-card-field-value"><?= $movie->year ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 2', 'task2-body');