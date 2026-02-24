<?php
/**
 * Завдання 2: Сортування міст у зворотному порядку
 *
 * Варіант 30 (група C): rsort — зворотно за алфавітом
 */
require_once __DIR__ . '/layout.php';

/**
 * Сортує міста у зворотному алфавітному порядку
 */
function sortCitiesReverse(string $input): array
{
    $cities = array_filter(array_map('trim', explode(' ', $input)));
    rsort($cities);
    return $cities;
}

// Вхідні дані (варіант 30)
$input = $_POST['cities'] ?? '';
$submitted = isset($_POST['cities']);
$defaultCities = 'Краматорськ Ладижин Бердянськ Шепетівка Новомосковськ Ромни Генічеськ Трускавець';

if (!$submitted) {
    $input = $defaultCities;
}

$sorted = sortCitiesReverse($input);

ob_start();
?>
<div class="demo-card">
    <h2>Сортування міст (зворотне)</h2>
    <p class="demo-subtitle">Введіть назви міст через пробіл — сортування від Я до А</p>

    <form method="post" class="demo-form">
        <div>
            <label for="cities">Міста (через пробіл)</label>
            <input type="text" id="cities" name="cities" value="<?= htmlspecialchars($input) ?>" placeholder="Краматорськ Ладижин Бердянськ">
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
        <h3 class="demo-section-title-success">Відсортовані (Я→А)</h3>
        <div class="array-display">
            <?php foreach ($sorted as $city): ?>
            <span class="array-item array-item-unique"><?= htmlspecialchars($city) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-code">sortCitiesReverse("<?= htmlspecialchars($input) ?>")
// rsort() — зворотний алфавітний порядок
// Результат: [<?= htmlspecialchars(implode(', ', array_map(fn($c) => "\"$c\"", $sorted))) ?>]</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 2');
