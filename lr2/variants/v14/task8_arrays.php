<?php
/**
 * Завдання 8: Операції з масивами
 *
 * Варіант 30 (група C): array_intersect + sort ascending
 * createArray(): довжина 3-6, значення 10-30
 */
require_once __DIR__ . '/layout.php';

/**
 * Створює масив випадкової довжини (3-6) з випадковими значеннями (10-30)
 */
function createArray(): array
{
    $length = random_int(3, 6);
    $arr = [];
    for ($i = 0; $i < $length; $i++) {
        $arr[] = random_int(10, 30);
    }
    return $arr;
}

/**
 * Знаходить спільні елементи двох масивів і сортує за зростанням
 */
function intersectSorted(array $a, array $b): array
{
    $common = array_intersect($a, $b);
    sort($common);
    return array_values($common);
}

// Генеруємо масиви (варіант 30)
$arr1 = createArray();
$arr2 = createArray();

$result = intersectSorted($arr1, $arr2);

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Операції з масивами</h2>
    <p class="demo-subtitle">createArray(), перетин (array_intersect), сортування за зростанням</p>

    <form method="post" class="demo-form">
        <button type="submit" name="regenerate" class="btn-submit">Згенерувати нові масиви</button>
    </form>

    <div class="demo-section">
        <h3>Масив 1</h3>
        <div class="array-display">
            <?php foreach ($arr1 as $v): ?>
            <span class="array-item <?= in_array($v, $result) ? 'array-item-unique' : '' ?>"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-section">
        <h3>Масив 2</h3>
        <div class="array-display">
            <?php foreach ($arr2 as $v): ?>
            <span class="array-item <?= in_array($v, $result) ? 'array-item-unique' : '' ?>"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595; Перетин (спільні елементи)</div>

    <div>
        <h3 class="demo-section-title-success">Результат (відсортований за зростанням)</h3>
        <?php if (!empty($result)): ?>
        <div class="array-display">
            <?php foreach ($result as $v): ?>
            <span class="array-item array-item-unique"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="demo-subtitle">Спільних елементів не знайдено</p>
        <?php endif; ?>
    </div>

    <div class="demo-code">$a = createArray(); // [<?= implode(', ', $arr1) ?>]
$b = createArray(); // [<?= implode(', ', $arr2) ?>]
intersectSorted($a, $b);
// array_intersect → sort
// Результат: [<?= implode(', ', $result) ?>]</div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 8');
