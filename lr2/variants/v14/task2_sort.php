<?php
/**
 * Завдання 2: Сортування міст за довжиною назви
 *
 * Критерій:
 * 1) від короткої до довгої
 * 2) якщо довжина однакова — за алфавітом
 */

require_once __DIR__ . '/layout.php';

/**
 * Сортує міста за довжиною назви
 */
function sortCitiesByLength(string $input): array
{
    $cities = array_filter(array_map('trim', explode(' ', $input)));

    usort($cities, function ($a, $b) {

  $lenA = strlen($a);
$lenB = strlen($b);

        // якщо довжина однакова — сортуємо за алфавітом
        if ($lenA == $lenB) {
            return strcmp($a, $b);
        }

        // сортування за довжиною
        return $lenA - $lenB;
    });

    return $cities;
}

// Вхідні дані
$input = $_POST['cities'] ?? '';
$submitted = isset($_POST['cities']);
$defaultCities = 'Бар Ічня Ніжин Ковель Одеса Кременчук Маріуполь Краматорськ';

if (!$submitted) {
    $input = $defaultCities;
}

$sorted = sortCitiesByLength($input);

ob_start();
?>

<div class="demo-card">

    <h2>Сортування міст за довжиною</h2>
    <p class="demo-subtitle">Критерій: від короткої до довгої, при однаковій довжині — за алфавітом</p>

    <form method="post" class="demo-form">
        <div>
            <label for="cities">Міста (через пробіл)</label>
            <input type="text"
                   id="cities"
                   name="cities"
                   value="<?= htmlspecialchars($input) ?>"
                   placeholder="Бар Ічня Ніжин Ковель">
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
    <h3 class="demo-section-title-success">Відсортовані</h3>

    <div class="array-display">
        <?php foreach ($sorted as $city): ?>
        <span class="array-item array-item-unique"><?= htmlspecialchars($city) ?></span>
        <?php endforeach; ?>
    </div>
</div>

<div class="demo-code">
sortCitiesByLength("<?= htmlspecialchars($input) ?>")

// 1. Сортування за довжиною (mb_strlen)
// 2. Якщо довжина однакова — strcmp()

// Результат:
[<?= htmlspecialchars(implode(', ', array_map(fn($c) => "\"$c\"", $sorted))) ?>]
</div>

<?php endif; ?>

</div>

<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 2');
?>