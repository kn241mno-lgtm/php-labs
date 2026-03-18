<?php
/**
 * Завдання 9: Асоціативний масив
 *
 * Варіант 14: ім'я => оцінка
 * Сортування: ksort (за іменем), asort (за оцінкою)
 */
require_once __DIR__ . '/layout.php';

/**
 * Сортує асоціативний масив за іменами (ключами)
 */
function sortByName(array $students): array
{
    ksort($students);
    return $students;
}

/**
 * Сортує асоціативний масив за оцінкою (значеннями)
 */
function sortByGrade(array $students): array
{
    asort($students);
    return $students;
}

// Дані (варіант 14)
$students = [
    'Баранник Олена' => 7,
    'Гриценко Павло' => 11,
    'Жуков Тетяна' => 2,
    'Кисіль Андрій' => 9,
    'Найда Катерина' => 6,
    'Романюк Сергій' => 12,
    'Хоменко Ірина' => 4,
];

// Обробка
$sortBy = $_POST['sort'] ?? $_GET['sort'] ?? 'name';
$sorted = $sortBy === 'grade' ? sortByGrade($students) : sortByName($students);

ob_start();
?>
<div class="demo-card">
    <h2>Асоціативний масив</h2>
    <p class="demo-subtitle">Сортування за іменем або за оцінкою</p>

    <div class="flex-buttons">
        <form method="post">
            <input type="hidden" name="sort" value="name">
            <button type="submit" class="<?= $sortBy === 'name' ? 'btn-submit' : 'btn-secondary' ?>">За іменем</button>
        </form>
        <form method="post">
            <input type="hidden" name="sort" value="grade">
            <button type="submit" class="<?= $sortBy === 'grade' ? 'btn-submit' : 'btn-secondary' ?>">За оцінкою</button>
        </form>
    </div>

    <div class="demo-section">
        <h3>Вхідні дані</h3>
        <div class="demo-code">$students = [
<?php foreach ($students as $name => $grade): ?>
    "<?= $name ?>" => <?= $grade ?>,
<?php endforeach; ?>
];</div>
    </div>

    <div class="demo-section">
        <h3>Відсортовано: <span class="demo-tag demo-tag-primary"><?= $sortBy === 'grade' ? 'за оцінкою' : 'за іменем' ?></span></h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ім'я <?= $sortBy === 'name' ? '&#8593;' : '' ?></th>
                    <th>Оцінка <?= $sortBy === 'grade' ? '&#8593;' : '' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($sorted as $name => $grade): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><span class="demo-tag demo-tag-success"><?= $grade ?> / 12</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="demo-code"><?= $sortBy === 'grade' ? 'sortByGrade' : 'sortByName' ?>($students);
// <?= $sortBy === 'grade' ? 'asort($students)' : 'ksort($students)' ?></div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 9');
