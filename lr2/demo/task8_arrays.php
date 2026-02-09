<?php
/**
 * Завдання 8: Операції з масивами
 *
 * Демонстрація: createArray(), з'єднання, видалення дублікатів, сортування
 */
require_once __DIR__ . '/layout.php';

/**
 * Створює масив випадкової довжини (3-7) з випадковими значеннями (10-20)
 */
function createArray(): array
{
    $length = random_int(3, 7);
    $arr = [];
    for ($i = 0; $i < $length; $i++) {
        $arr[] = random_int(10, 20);
    }
    return $arr;
}

/**
 * З'єднує два масиви, видаляє дублікати, сортує за зростанням
 */
function mergeUniqueSorted(array $a, array $b): array
{
    $merged = array_merge($a, $b);
    $unique = array_unique($merged);
    sort($unique);
    return array_values($unique);
}

// Генеруємо або використовуємо збережені масиви
$submitted = isset($_POST['regenerate']) || isset($_POST['merge']);

$arr1 = createArray();
$arr2 = createArray();

$merged = array_merge($arr1, $arr2);
$result = mergeUniqueSorted($arr1, $arr2);

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Операції з масивами</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">createArray(), з'єднання, видалення дублікатів, сортування</p>

    <form method="post" class="demo-form">
        <button type="submit" name="regenerate" class="btn-submit">Згенерувати нові масиви</button>
    </form>

    <div class="demo-section">
        <h3>Масив 1 <span style="color: var(--color-text-muted); font-weight: normal;">(createArray())</span></h3>
        <div class="array-display">
            <?php foreach ($arr1 as $v): ?>
            <span class="array-item"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-section" style="border: none; padding-top: 12px;">
        <h3>Масив 2 <span style="color: var(--color-text-muted); font-weight: normal;">(createArray())</span></h3>
        <div class="array-display">
            <?php foreach ($arr2 as $v): ?>
            <span class="array-item"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595; З'єднання</div>

    <div>
        <h3 style="margin: 0 0 8px; font-size: 14px; color: var(--color-text-muted);">Об'єднаний масив</h3>
        <div class="array-display">
            <?php
            $dupValues = array_diff_key($merged, array_unique($merged));
            $dupList = array_unique(array_values($dupValues));
            foreach ($merged as $v):
            ?>
            <span class="array-item <?= in_array($v, $dupList) ? 'array-item-dup' : '' ?>"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
            <span style="color: var(--color-error-text);">Червоні</span> — дублікати, що будуть видалені
        </p>
    </div>

    <div class="array-arrow">&#8595; Унікальні + сортування</div>

    <div>
        <h3 style="margin: 0 0 8px; font-size: 14px; color: var(--color-success-text);">Результат</h3>
        <div class="array-display">
            <?php foreach ($result as $v): ?>
            <span class="array-item array-item-unique"><?= $v ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-code">$a = createArray(); // [<?= implode(', ', $arr1) ?>]
$b = createArray(); // [<?= implode(', ', $arr2) ?>]
mergeUniqueSorted($a, $b);
// Результат: [<?= implode(', ', $result) ?>]</div>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Масиви: Операції');
