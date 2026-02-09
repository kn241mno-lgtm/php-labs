<?php
/**
 * Завдання 9: Асоціативний масив
 *
 * Демонстрація: ключі — імена, значення — вік. Сортування за параметром.
 */
require_once __DIR__ . '/layout.php';

/**
 * Сортує асоціативний масив за іменами (ключами)
 */
function sortByName(array $people): array
{
    ksort($people);
    return $people;
}

/**
 * Сортує асоціативний масив за віком (значеннями)
 */
function sortByAge(array $people): array
{
    asort($people);
    return $people;
}

// Дані
$people = [
    'Олена' => 28,
    'Андрій' => 35,
    'Марія' => 22,
    'Дмитро' => 31,
    'Ірина' => 19,
    'Богдан' => 42,
    'Катерина' => 25,
    'Василь' => 38,
];

// Обробка
$sortBy = $_POST['sort'] ?? $_GET['sort'] ?? 'name';
$sorted = $sortBy === 'age' ? sortByAge($people) : sortByName($people);

ob_start();
?>
<div class="demo-card">
    <h2>Асоціативний масив</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Сортування за іменем або за віком</p>

    <div class="flex-buttons" style="margin-bottom: 20px; justify-content: center;">
        <form method="post" style="display: inline;">
            <input type="hidden" name="sort" value="name">
            <button type="submit" class="<?= $sortBy === 'name' ? 'btn-submit' : 'btn-secondary' ?>">За іменем</button>
        </form>
        <form method="post" style="display: inline;">
            <input type="hidden" name="sort" value="age">
            <button type="submit" class="<?= $sortBy === 'age' ? 'btn-submit' : 'btn-secondary' ?>">За віком</button>
        </form>
    </div>

    <div class="demo-section" style="border-top: none; padding-top: 0;">
        <h3>Вхідні дані</h3>
        <div class="demo-code" style="text-align: left;">$people = [
<?php foreach ($people as $name => $age): ?>
    "<?= $name ?>" => <?= $age ?>,
<?php endforeach; ?>
];</div>
    </div>

    <div class="demo-section">
        <h3>Відсортовано: <span class="demo-tag demo-tag-primary"><?= $sortBy === 'age' ? 'за віком' : 'за іменем' ?></span></h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ім'я <?= $sortBy === 'name' ? '&#8593;' : '' ?></th>
                    <th>Вік <?= $sortBy === 'age' ? '&#8593;' : '' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($sorted as $name => $age): ?>
                <tr>
                    <td style="color: var(--color-text-muted);"><?= $i++ ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($name) ?></td>
                    <td><span class="demo-tag demo-tag-success"><?= $age ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="demo-code"><?= $sortBy === 'age' ? 'sortByAge' : 'sortByName' ?>($people);
// <?= $sortBy === 'age' ? 'asort($people)' : 'ksort($people)' ?></div>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Масиви: Асоціативний');
