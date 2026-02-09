<?php
/**
 * Завдання 3: Ім'я файлу
 *
 * Демонстрація: виділення імені файлу без розширення з повного шляху
 */
require_once __DIR__ . '/layout.php';

/**
 * Виділяє ім'я файлу без розширення з повного шляху
 */
function extractFilename(string $path): string
{
    // Отримуємо базове ім'я файлу (працює з \ і /)
    $basename = basename(str_replace('\\', '/', $path));
    // Видаляємо розширення
    $dotPos = strrpos($basename, '.');
    if ($dotPos !== false) {
        return substr($basename, 0, $dotPos);
    }
    return $basename;
}

/**
 * Виділяє розширення файлу
 */
function extractExtension(string $path): string
{
    $basename = basename(str_replace('\\', '/', $path));
    $dotPos = strrpos($basename, '.');
    if ($dotPos !== false) {
        return substr($basename, $dotPos + 1);
    }
    return '';
}

/**
 * Виділяє директорію
 */
function extractDirectory(string $path): string
{
    $normalized = str_replace('\\', '/', $path);
    return dirname($normalized);
}

// Обробка форми
$path = $_POST['path'] ?? 'D:\Projects\Web\mysite\images\myfile.txt';
$submitted = isset($_POST['path']);

$filename = extractFilename($path);
$extension = extractExtension($path);
$directory = extractDirectory($path);

ob_start();
?>
<div class="demo-card">
    <h2>Виділення імені файлу</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Отримання імені файлу без розширення з повного шляху</p>

    <form method="post" class="demo-form">
        <div>
            <label for="path">Повний шлях до файлу</label>
            <input type="text" id="path" name="path" value="<?= htmlspecialchars($path) ?>" placeholder="D:\folder\file.txt">
        </div>
        <button type="submit" class="btn-submit">Виділити</button>
    </form>

    <div class="demo-result">
        <h3>Ім'я файлу (без розширення)</h3>
        <div class="demo-result-value"><?= htmlspecialchars($filename) ?></div>
    </div>

    <div class="demo-section">
        <h3>Деталі розбору</h3>
        <table class="demo-table">
            <tr>
                <td style="font-weight: 600;">Повний шлях</td>
                <td><code><?= htmlspecialchars($path) ?></code></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Директорія</td>
                <td><code><?= htmlspecialchars($directory) ?></code></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Ім'я файлу</td>
                <td><span class="demo-tag demo-tag-success"><?= htmlspecialchars($filename) ?></span></td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Розширення</td>
                <td><span class="demo-tag demo-tag-primary"><?= htmlspecialchars($extension) ?></span></td>
            </tr>
        </table>
    </div>

    <div class="demo-code">extractFilename("<?= htmlspecialchars($path) ?>")
// Результат: "<?= htmlspecialchars($filename) ?>"</div>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Рядки: Ім\'я файлу');
