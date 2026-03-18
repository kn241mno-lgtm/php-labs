<?php
/**
 * Завдання 6: Пошук унікальних елементів
 *
 * Варіант 14 (група C): унікальні елементи
 * Масив: [7, 3, 14, 3, 10, 7, 6, 14, 1, 6, 11, 5] → [10, 1, 11, 5]
 */
require_once __DIR__ . '/layout.php';

/**
 * Знаходить унікальні елементи (ті, що зустрічаються лише один раз) в масиві
 *
 * @return array
 */
function findUnique(array $arr): array
{
    $seen = [];
    $unique = [];
    foreach ($arr as $item) {
        if (!isset($seen[$item])) {
            $seen[$item] = 0;
        }
        $seen[$item]++;
        if ($seen[$item] == 1) {
            $unique[] = $item;
        } elseif ($seen[$item] == 2) {
            $key = array_search($item, $unique);
            if ($key !== false) {
                unset($unique[$key]);
                $unique = array_values($unique);
            }
        }
    }
    return $unique;
}

// Обробка форми (варіант 14)
$input = $_POST['array'] ?? '7, 3, 14, 3, 10, 7, 6, 14, 1, 6, 11, 5';
$submitted = isset($_POST['array']);

$arr = array_map('trim', explode(',', $input));
$arr = array_filter($arr, fn($v) => $v !== '');

$unique = findUnique($arr);

ob_start();
?>
<div class="demo-card">
    <h2>Унікальні елементи</h2>
    <p class="demo-subtitle">Знаходить елементи, що зустрічаються лише один раз в масиві</p>

    <form method="post" class="demo-form">
        <div>
            <label for="array">Масив (через кому)</label>
            <input type="text" id="array" name="array" value="<?= htmlspecialchars($input) ?>" placeholder="1, 4, 1, 1, 6">
        </div>
        <button type="submit" class="btn-submit">Знайти унікальні</button>
    </form>

    <?php if (!empty($arr)): ?>
    <div class="demo-section">
        <h3>Вхідний масив</h3>
        <div class="array-display">
            <?php foreach ($arr as $item): ?>
            <span class="array-item <?= in_array(trim($item), $unique) ? 'array-item-unique' : '' ?>"><?= htmlspecialchars($item) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($unique): ?>
    <div class="demo-result">
        <h3>Унікальні елементи</h3>
        <div class="demo-result-value">[<?= htmlspecialchars(implode(', ', $unique)) ?>]</div>
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
                        <?php if ($count == 1): ?>
                        <span class="demo-tag demo-tag-success">Унікальний</span>
                        <?php else: ?>
                        <span class="demo-tag demo-tag-primary">Дублікат (<?= $count ?>×)</span>
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
        <div class="demo-result-value">Масив порожній або немає унікальних елементів</div>
    </div>
    <?php endif; ?>

    <div class="demo-code">findUnique([<?= htmlspecialchars(implode(', ', $arr)) ?>])
// Результат: [<?= htmlspecialchars(implode(', ', $unique)) ?>]</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 6');
