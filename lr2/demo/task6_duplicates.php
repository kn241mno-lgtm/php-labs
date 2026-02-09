<?php
/**
 * Завдання 6: Дублікати
 *
 * Демонстрація: функція знаходить елементи, що повторюються в масиві
 */
require_once __DIR__ . '/layout.php';

/**
 * Знаходить елементи, що повторюються в масиві
 *
 * @param array $arr Вхідний масив
 * @return array Масив дублікатів (унікальних)
 */
function findDuplicates(array $arr): array
{
    $counts = array_count_values($arr);
    return array_reduce(array_keys($counts), function (array $acc, $key) use ($counts) {
        if ($counts[$key] > 1) {
            $acc[] = $key;
        }
        return $acc;
    }, []);
}

// Обробка форми
$input = $_POST['array'] ?? '5, 3, 8, 3, 1, 5, 9, 8, 5, 2';
$submitted = isset($_POST['array']);

// Парсимо вхідний рядок в масив
$arr = array_map('trim', explode(',', $input));
$arr = array_filter($arr, fn($v) => $v !== '');

$duplicates = findDuplicates($arr);

ob_start();
?>
<div class="demo-card">
    <h2>Пошук дублікатів</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Знаходить елементи, що повторюються в масиві</p>

    <form method="post" class="demo-form">
        <div>
            <label for="array">Масив (через кому)</label>
            <input type="text" id="array" name="array" value="<?= htmlspecialchars($input) ?>" placeholder="1, 2, 3, 2, 4">
        </div>
        <button type="submit" class="btn-submit">Знайти дублікати</button>
    </form>

    <?php if (!empty($arr)): ?>
    <div class="demo-section">
        <h3>Вхідний масив</h3>
        <div class="array-display">
            <?php foreach ($arr as $item): ?>
            <span class="array-item <?= in_array($item, $duplicates) ? 'array-item-dup' : '' ?>"><?= htmlspecialchars($item) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($duplicates)): ?>
    <div class="demo-result">
        <h3>Дублікати</h3>
        <div class="array-display" style="margin-top: 8px;">
            <?php foreach ($duplicates as $dup): ?>
            <span class="demo-tag demo-tag-error" style="font-size: 16px; padding: 6px 14px;"><?= htmlspecialchars($dup) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="demo-result demo-result-info">
        <h3>Результат</h3>
        <div class="demo-result-value">Дублікатів не знайдено</div>
    </div>
    <?php endif; ?>

    <div class="demo-code">findDuplicates([<?= htmlspecialchars(implode(', ', array_map(fn($v) => "\"$v\"", $arr))) ?>])
// Результат: [<?= htmlspecialchars(implode(', ', array_map(fn($v) => "\"$v\"", $duplicates))) ?>]</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Масиви: Дублікати');
