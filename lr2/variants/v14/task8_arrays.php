<?php
/**
 * Завдання 8: Операції з масивами
 *
 * createArray(): довжина 4-8, значення 1-50
 * Операція: об'єднання → унікальні значення → сортування за спаданням
 */
require_once __DIR__ . '/layout.php';

/**
 * Створює масив випадкової довжини (4-8) з випадковими значеннями (1-50)
 */
function createArray(): array
{
    $length = random_int(4, 8);
    $arr = [];
    for ($i = 0; $i < $length; $i++) {
        $arr[] = random_int(1, 50);
    }
    return $arr;
}

/**
 * Об'єднує масиви, видаляє дублікати і сортує за спаданням
 */
function mergeUniqueSorted(array $a, array $b): array
{
    $merged = array_merge($a, $b);   // об'єднання
    $unique = array_unique($merged); // видалення дублікатів
    rsort($unique);                  // сортування за спаданням
    return array_values($unique);    // переіндексація
}

// Генеруємо масиви
$arr1 = createArray();
$arr2 = createArray();

$result = mergeUniqueSorted($arr1, $arr2);

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Операції з масивами</h2>
    <p class="demo-subtitle">createArray(), об'єднання (array_merge), унікальні (array_unique), сортування за спаданням</p>

    <form method="post" class="demo-form">
        <button type="submit" name="regenerate" class="btn-submit">Згенерувати нові масиви</button>
    </form>

    <div class="demo-section">
        <h3>Масив 1</h3>
        <div class="array-display">
            <?php foreach ($arr1 as $v): ?>
            <span class="array-item"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-section">
        <h3>Масив 2</h3>
        <div class="array-display">
            <?php foreach ($arr2 as $v): ?>
            <span class="array-item"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595; Об'єднання → унікальні → спадання</div>

    <div>
        <h3 class="demo-section-title-success">Результат</h3>
        <div class="array-display">
            <?php foreach ($result as $v): ?>
            <span class="array-item array-item-unique"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-code">
$a = createArray(); // [<?= implode(', ', $arr1) ?>]
$b = createArray(); // [<?= implode(', ', $arr2) ?>]
mergeUniqueSorted($a, $b);
// array_merge → array_unique → rsort
// Результат: [<?= implode(', ', $result) ?>]
    </div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 8');