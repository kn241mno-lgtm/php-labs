<?php
/**
 * Завдання 9: Асоціативний масив
 *
 * Варіант 30 (група C): ім'я => зарплата (замість ім'я => вік)
 * Сортування: ksort (за іменем), asort (за зарплатою)
 */
require_once __DIR__ . '/layout.php';

/**
 * Сортує асоціативний масив за іменами (ключами)
 */
function sortByName(array $employees): array
{
    ksort($employees);
    return $employees;
}

/**
 * Сортує асоціативний масив за зарплатою (значеннями)
 */
function sortBySalary(array $employees): array
{
    asort($employees);
    return $employees;
}

// Дані (варіант 30)
$employees = [
    'Микола' => 44000,
    'Жанна' => 67500,
    'Ростислав' => 31000,
    'Тамара' => 58000,
    'Юрій' => 22000,
    'Елла' => 75000,
    'Захар' => 40000,
];

// Обробка
$sortBy = $_POST['sort'] ?? $_GET['sort'] ?? 'name';
$sorted = $sortBy === 'salary' ? sortBySalary($employees) : sortByName($employees);

ob_start();
?>
<div class="demo-card">
    <h2>Асоціативний масив</h2>
    <p class="demo-subtitle">Сортування за іменем або за зарплатою</p>

    <div class="flex-buttons">
        <form method="post">
            <input type="hidden" name="sort" value="name">
            <button type="submit" class="<?= $sortBy === 'name' ? 'btn-submit' : 'btn-secondary' ?>">За іменем</button>
        </form>
        <form method="post">
            <input type="hidden" name="sort" value="salary">
            <button type="submit" class="<?= $sortBy === 'salary' ? 'btn-submit' : 'btn-secondary' ?>">За зарплатою</button>
        </form>
    </div>

    <div class="demo-section">
        <h3>Вхідні дані</h3>
        <div class="demo-code">$employees = [
<?php foreach ($employees as $name => $salary): ?>
    "<?= $name ?>" => <?= number_format($salary, 0, '', ' ') ?>,
<?php endforeach; ?>
];</div>
    </div>

    <div class="demo-section">
        <h3>Відсортовано: <span class="demo-tag demo-tag-primary"><?= $sortBy === 'salary' ? 'за зарплатою' : 'за іменем' ?></span></h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ім'я <?= $sortBy === 'name' ? '&#8593;' : '' ?></th>
                    <th>Зарплата <?= $sortBy === 'salary' ? '&#8593;' : '' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($sorted as $name => $salary): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><span class="demo-tag demo-tag-success"><?= number_format($salary, 0, '', ' ') ?> ₴</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="demo-code"><?= $sortBy === 'salary' ? 'sortBySalary' : 'sortByName' ?>($employees);
// <?= $sortBy === 'salary' ? 'asort($employees)' : 'ksort($employees)' ?></div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 9');
