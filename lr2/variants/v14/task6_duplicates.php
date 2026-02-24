<?php
/**
 * Завдання 6: Найчастіший елемент (мода)
 *
 * Варіант 30 (група C): мода замість дублікатів
 * Масив: [1, 4, 1, 1, 6, 1, 3, 1, 9, 7, 1] → 1 (6 разів)
 */
require_once __DIR__ . '/layout.php';

/**
 * Знаходить найчастіший елемент (моду) в масиві
 *
 * @return array{value: mixed, count: int}|null
 */
function findMode(array $arr): ?array
{
    if (empty($arr)) {
        return null;
    }

    $counts = array_count_values($arr);
    $maxCount = max($counts);
    $modeValue = array_search($maxCount, $counts);

    return ['value' => $modeValue, 'count' => $maxCount];
}

// Обробка форми (варіант 30)
$input = $_POST['array'] ?? '1, 4, 1, 1, 6, 1, 3, 1, 9, 7, 1';
$submitted = isset($_POST['array']);

$arr = array_map('trim', explode(',', $input));
$arr = array_filter($arr, fn($v) => $v !== '');

$mode = findMode($arr);

ob_start();
?>
<div class="demo-card">
    <h2>Найчастіший елемент (мода)</h2>
    <p class="demo-subtitle">Знаходить елемент, що зустрічається найчастіше в масиві</p>

    <form method="post" class="demo-form">
        <div>
            <label for="array">Масив (через кому)</label>
            <input type="text" id="array" name="array" value="<?= htmlspecialchars($input) ?>" placeholder="1, 4, 1, 1, 6">
        </div>
        <button type="submit" class="btn-submit">Знайти моду</button>
    </form>

    <?php if (!empty($arr)): ?>
    <div class="demo-section">
        <h3>Вхідний масив</h3>
        <div class="array-display">
            <?php foreach ($arr as $item): ?>
            <span class="array-item <?= $mode && trim($item) == $mode['value'] ? 'array-item-unique' : '' ?>"><?= htmlspecialchars($item) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($mode): ?>
    <div class="demo-result">
        <h3>Мода</h3>
        <div class="demo-result-value"><?= htmlspecialchars($mode['value']) ?> (зустрічається <?= $mode['count'] ?> разів)</div>
    </div>

    <div class="demo-section">
        <h3>Частота елементів</h3>
        <table class="demo-table">
            <thead>
                <tr>
                    <th>Елемент</th>
                    <th>Кількість</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counts = array_count_values($arr);
                arsort($counts);
                foreach ($counts as $value => $count):
                ?>
                <tr>
                    <td><?= htmlspecialchars($value) ?></td>
                    <td><?= $count ?></td>
                    <td>
                        <?php if ($value == $mode['value']): ?>
                        <span class="demo-tag demo-tag-success">Мода</span>
                        <?php else: ?>
                        <span class="demo-tag demo-tag-primary"><?= $count ?>×</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="demo-result demo-result-info">
        <h3>Результат</h3>
        <div class="demo-result-value">Масив порожній</div>
    </div>
    <?php endif; ?>

    <div class="demo-code">findMode([<?= htmlspecialchars(implode(', ', $arr)) ?>])
// Результат: <?= $mode ? "мода = {$mode['value']} ({$mode['count']} разів)" : 'null' ?></div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 6');
