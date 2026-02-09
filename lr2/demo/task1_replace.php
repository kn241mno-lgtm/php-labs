<?php
/**
 * Завдання 1: Знайти і замінити
 *
 * Демонстрація: робота з рядками, str_replace
 */
require_once __DIR__ . '/layout.php';

/**
 * Замінює всі входження підрядка в тексті
 */
function findAndReplace(string $text, string $find, string $replace): string
{
    if ($find === '') {
        return $text;
    }
    return str_replace($find, $replace, $text);
}

// Обробка форми
$text = $_POST['text'] ?? 'Привіт, світ! Це чудовий світ програмування.';
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
    <p style="color: var(--color-text-muted); margin-top: 0;">Заміна символів/підрядків у тексті</p>

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
renderDemoLayout($content, 'Рядки: Заміна');
