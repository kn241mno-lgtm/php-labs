<?php
/**
 * Завдання 2: Сортування міст
 *
 * Демонстрація: робота з рядками, explode, sort, implode
 */
require_once __DIR__ . '/layout.php';

/**
 * Сортує міста в алфавітному порядку
 */
function sortCities(string $input): array
{
    $cities = array_filter(array_map('trim', explode(' ', $input)));
    sort($cities);
    return $cities;
}

// Обробка форми
$input = $_POST['cities'] ?? '';
$submitted = isset($_POST['cities']);
$defaultCities = 'Київ Львів Одеса Харків Дніпро Запоріжжя Вінниця Полтава';

if (!$submitted) {
    $input = $defaultCities;
}

$sorted = sortCities($input);

ob_start();
?>
<div class="demo-card">
    <h2>Сортування міст</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Введіть назви міст через пробіл</p>

    <form method="post" class="demo-form">
        <div>
            <label for="cities">Міста (через пробіл)</label>
            <input type="text" id="cities" name="cities" value="<?= htmlspecialchars($input) ?>" placeholder="Київ Львів Одеса">
        </div>
        <button type="submit" class="btn-submit">Сортувати</button>
    </form>

    <?php if (!empty($sorted)): ?>
    <div class="demo-section">
        <h3>Вхідні дані</h3>
        <div class="array-display">
            <?php foreach (array_filter(array_map('trim', explode(' ', $input))) as $city): ?>
            <span class="array-item"><?= htmlspecialchars($city) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595;</div>

    <div>
        <h3 style="margin: 0 0 12px; font-size: 16px; color: var(--color-success-text);">Відсортовані</h3>
        <div class="array-display">
            <?php foreach ($sorted as $city): ?>
            <span class="array-item array-item-unique"><?= htmlspecialchars($city) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-code">sortCities("<?= htmlspecialchars($input) ?>")
// Результат: [<?= htmlspecialchars(implode(', ', array_map(fn($c) => "\"$c\"", $sorted))) ?>]</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Рядки: Сортування');
