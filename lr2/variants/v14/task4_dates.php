<?php
/**
 * Завдання 4: Різниця дат + день тижня
 *
 * Варіант 30 (група C, Sub3): дні + день тижня (українською)
 * Дати: 08-08-2024 та 12-04-2026 → 612 днів, четвер — неділя
 */
require_once __DIR__ . '/layout.php';

function dateDifference(string $date1, string $date2): int|false
{
    $d1 = DateTime::createFromFormat('d-m-Y', $date1);
    $d2 = DateTime::createFromFormat('d-m-Y', $date2);

    if (!$d1 || !$d2) {
        return false;
    }

    $interval = $d1->diff($d2);
    return $interval->days;
}

function isValidDate(string $date): bool
{
    $d = DateTime::createFromFormat('d-m-Y', $date);
    return $d && $d->format('d-m-Y') === $date;
}

/**
 * Повертає назву дня тижня українською
 */
function getWeekdayUkrainian(string $date): string
{
    $days = [
        'Monday' => 'понеділок',
        'Tuesday' => 'вівторок',
        'Wednesday' => 'середа',
        'Thursday' => 'четвер',
        'Friday' => 'п\'ятниця',
        'Saturday' => 'субота',
        'Sunday' => 'неділя',
    ];
    $d = DateTime::createFromFormat('d-m-Y', $date);
    if (!$d) {
        return '';
    }
    return $days[$d->format('l')] ?? '';
}

// Вхідні дані (варіант 30)
$date1 = $_POST['date1'] ?? '08-08-2024';
$date2 = $_POST['date2'] ?? '12-04-2026';
$submitted = isset($_POST['date1']);

$error = '';
$days = null;

if ($submitted) {
    if (!isValidDate($date1)) {
        $error = "Перша дата має невірний формат. Використовуйте ДД-ММ-РРРР";
    } elseif (!isValidDate($date2)) {
        $error = "Друга дата має невірний формат. Використовуйте ДД-ММ-РРРР";
    } else {
        $days = dateDifference($date1, $date2);
    }
}

ob_start();
?>
<div class="demo-card">
    <h2>Різниця дат + день тижня</h2>
    <p class="demo-subtitle">Кількість днів між датами + назва дня тижня (українською)</p>

    <form method="post" class="demo-form">
        <div class="form-row">
            <div>
                <label for="date1">Перша дата</label>
                <input type="text" id="date1" name="date1" value="<?= htmlspecialchars($date1) ?>" placeholder="ДД-ММ-РРРР">
            </div>
            <div>
                <label for="date2">Друга дата</label>
                <input type="text" id="date2" name="date2" value="<?= htmlspecialchars($date2) ?>" placeholder="ДД-ММ-РРРР">
            </div>
        </div>
        <button type="submit" class="btn-submit">Обчислити</button>
    </form>

    <?php if ($error): ?>
    <div class="demo-result demo-result-error">
        <h3>Помилка</h3>
        <div class="demo-result-value"><?= htmlspecialchars($error) ?></div>
    </div>
    <?php elseif ($days !== null): ?>
    <div class="demo-result">
        <h3>Різниця</h3>
        <div class="demo-result-value"><?= $days ?> днів</div>
    </div>

    <div class="demo-section">
        <h3>Деталі</h3>
        <table class="demo-table">
            <tr>
                <td class="demo-table-label">Дата 1</td>
                <td>
                    <span class="demo-tag demo-tag-primary"><?= htmlspecialchars($date1) ?></span>
                    — <?= htmlspecialchars(getWeekdayUkrainian($date1)) ?>
                </td>
            </tr>
            <tr>
                <td class="demo-table-label">Дата 2</td>
                <td>
                    <span class="demo-tag demo-tag-primary"><?= htmlspecialchars($date2) ?></span>
                    — <?= htmlspecialchars(getWeekdayUkrainian($date2)) ?>
                </td>
            </tr>
            <tr>
                <td class="demo-table-label">Різниця</td>
                <td><span class="demo-tag demo-tag-success"><?= $days ?> днів</span></td>
            </tr>
        </table>
    </div>

    <div class="demo-code">dateDifference("<?= htmlspecialchars($date1) ?>", "<?= htmlspecialchars($date2) ?>")
// Результат: <?= $days ?> днів
// getWeekdayUkrainian("<?= htmlspecialchars($date1) ?>") = "<?= htmlspecialchars(getWeekdayUkrainian($date1)) ?>"
// getWeekdayUkrainian("<?= htmlspecialchars($date2) ?>") = "<?= htmlspecialchars(getWeekdayUkrainian($date2)) ?>"</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 4');
