<?php
/**
 * Завдання 1: Пошук та заміна
 *
 * Варіант 30: "учням" → "студентам"
 */
require_once __DIR__ . '/layout.php';

function findAndReplace(string $text, string $find, string $replace): string
{
    if ($find === '') {
        return $text;
    }
    return str_replace($find, $replace, $text);
}

// Вхідні дані (варіант 30)
$text = $_POST['text'] ?? 'Вчитель пояснив учням нову тему з математики і попросив учнів виконати домашнє завдання до понеділка';
$find = $_POST['find'] ?? '';
$replace = $_POST['replace'] ?? '';
$result = '';
$submitted = isset($_POST['text']);

if ($submitted && $find !== '') {
    $result = findAndReplace($text, $find, $replace);
}

ob_start();
?>
<div class="demo-card">
    <h2>Знайти і замінити</h2>
    <p class="demo-subtitle">Заміна символів/підрядків у тексті</p>

    <form method="post" class="demo-form">
        <div>
            <label for="text">Текст</label>
            <textarea id="text" name="text" rows="3"><?= htmlspecialchars($text) ?></textarea>
        </div>
        <div class="form-row">
            <div>
                <label for="find">Знайти</label>
                <input type="text" id="find" name="find" value="<?= htmlspecialchars($find) ?>" placeholder="символи для пошуку">
            </div>
            <div>
                <label for="replace">Замінити на</label>
                <input type="text" id="replace" name="replace" value="<?= htmlspecialchars($replace) ?>" placeholder="нові символи">
            </div>
        </div>
        <button type="submit" class="btn-submit">Замінити</button>
    </form>

    <?php if ($submitted && $find !== ''): ?>
    <div class="demo-result">
        <h3>Результат</h3>
        <div class="demo-result-value"><?= htmlspecialchars($result) ?></div>
    </div>
    <div class="demo-code">findAndReplace(text, "<?= htmlspecialchars($find) ?>", "<?= htmlspecialchars($replace) ?>")</div>
    <?php elseif ($submitted && $find === ''): ?>
    <div class="demo-result demo-result-error">
        <h3>Помилка</h3>
        <div class="demo-result-value">Поле "Знайти" не може бути порожнім</div>
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 1');
