<?php
/**
 * Завдання 7: Генератор імен тварин
 *
 * Демонстрація: функція приймає масив складів і генерує ім'я
 */
require_once __DIR__ . '/layout.php';

/**
 * Генерує ім'я тварини з масиву складів
 *
 * @param array $syllables Масив складів
 * @param int $count Кількість складів в імені (2-4)
 * @return string Згенероване ім'я
 */
function generateAnimalName(array $syllables, int $count = 3): string
{
    if (empty($syllables)) {
        return '';
    }

    $count = max(2, min(4, $count));
    $name = '';

    for ($i = 0; $i < $count; $i++) {
        $name .= $syllables[array_rand($syllables)];
    }

    if (!function_exists('mb_strtoupper')) {
        return $name;
    }
    return mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
}

/**
 * Генерує кілька імен
 */
function generateMultipleNames(array $syllables, int $namesCount = 5, int $syllablesPerName = 3): array
{
    $names = [];
    for ($i = 0; $i < $namesCount; $i++) {
        $names[] = generateAnimalName($syllables, $syllablesPerName);
    }
    return $names;
}

// Обробка форми
$syllablesInput = $_POST['syllables'] ?? 'ба ку ри мі ло та ні ша зо пу';
$count = (int)($_POST['count'] ?? 5);
$syllablesPerName = (int)($_POST['syllables_per_name'] ?? 3);
$submitted = isset($_POST['syllables']);

if ($count < 1) $count = 1;
if ($count > 20) $count = 20;
if ($syllablesPerName < 2) $syllablesPerName = 2;
if ($syllablesPerName > 4) $syllablesPerName = 4;

$syllables = array_filter(array_map('trim', explode(' ', $syllablesInput)));
$names = [];

if (!empty($syllables)) {
    $names = generateMultipleNames($syllables, $count, $syllablesPerName);
}

ob_start();
?>
<div class="demo-card">
    <h2>Генератор імен тварин</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Створює унікальні імена з набору складів</p>

    <form method="post" class="demo-form">
        <div>
            <label for="syllables">Склади (через пробіл)</label>
            <input type="text" id="syllables" name="syllables" value="<?= htmlspecialchars($syllablesInput) ?>" placeholder="ба ку рі мі ло">
        </div>
        <div class="form-row">
            <div>
                <label for="count">Кількість імен</label>
                <input type="number" id="count" name="count" value="<?= $count ?>" min="1" max="20">
            </div>
            <div>
                <label for="syllables_per_name">Складів в імені</label>
                <input type="number" id="syllables_per_name" name="syllables_per_name" value="<?= $syllablesPerName ?>" min="2" max="4">
            </div>
        </div>
        <button type="submit" class="btn-submit">Згенерувати</button>
    </form>

    <?php if (!empty($syllables)): ?>
    <div class="demo-section">
        <h3>Склади</h3>
        <div class="array-display">
            <?php foreach ($syllables as $s): ?>
            <span class="array-item"><?= htmlspecialchars($s) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="array-arrow">&#8595;</div>

    <div>
        <h3 style="margin: 0 0 12px; font-size: 16px; color: var(--color-success-text);">Згенеровані імена</h3>
        <div class="array-display">
            <?php foreach ($names as $name): ?>
            <span class="array-item array-item-unique" style="font-size: 17px;"><?= htmlspecialchars($name) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="demo-code">$syllables = [<?= htmlspecialchars(implode(', ', array_map(fn($s) => "\"$s\"", $syllables))) ?>];
generateAnimalName($syllables, <?= $syllablesPerName ?>)
// Приклад: "<?= htmlspecialchars($names[0] ?? '') ?>"</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Масиви: Імена тварин');
