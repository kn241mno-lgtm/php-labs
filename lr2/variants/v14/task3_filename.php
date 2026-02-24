<?php
/**
 * Завдання 3: Ім'я файлу
 *
 * Варіант 30: "/var/audio/podcasts/episode_42.mp3"
 */
require_once __DIR__ . '/layout.php';

function extractFilename(string $path): string
{
    $basename = basename(str_replace('\\', '/', $path));
    $dotPos = strrpos($basename, '.');
    if ($dotPos !== false) {
        return substr($basename, 0, $dotPos);
    }
    return $basename;
}

function extractExtension(string $path): string
{
    $basename = basename(str_replace('\\', '/', $path));
    $dotPos = strrpos($basename, '.');
    if ($dotPos !== false) {
        return substr($basename, $dotPos + 1);
    }
    return '';
}

function extractDirectory(string $path): string
{
    $normalized = str_replace('\\', '/', $path);
    return dirname($normalized);
}

// Вхідні дані (варіант 30)
$path = $_POST['path'] ?? '/var/audio/podcasts/episode_42.mp3';
$submitted = isset($_POST['path']);

$filename = extractFilename($path);
$extension = extractExtension($path);
$directory = extractDirectory($path);

ob_start();
?>
<div class="demo-card">
    <h2>Виділення імені файлу</h2>
    <p class="demo-subtitle">Отримання імені файлу без розширення з повного шляху</p>

    <form method="post" class="demo-form">
        <div>
            <label for="path">Повний шлях до файлу</label>
            <input type="text" id="path" name="path" value="<?= htmlspecialchars($path) ?>" placeholder="/var/audio/file.mp3">
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
                <td class="demo-table-label">Повний шлях</td>
                <td><code><?= htmlspecialchars($path) ?></code></td>
            </tr>
            <tr>
                <td class="demo-table-label">Директорія</td>
                <td><code><?= htmlspecialchars($directory) ?></code></td>
            </tr>
            <tr>
                <td class="demo-table-label">Ім'я файлу</td>
                <td><span class="demo-tag demo-tag-success"><?= htmlspecialchars($filename) ?></span></td>
            </tr>
            <tr>
                <td class="demo-table-label">Розширення</td>
                <td><span class="demo-tag demo-tag-primary"><?= htmlspecialchars($extension) ?></span></td>
            </tr>
        </table>
    </div>

    <div class="demo-code">extractFilename("<?= htmlspecialchars($path) ?>")
// Результат: "<?= htmlspecialchars($filename) ?>"</div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 3');
