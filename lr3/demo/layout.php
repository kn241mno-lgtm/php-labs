<?php
/**
 * Shared layout template for LR3 demo task pages
 *
 * Features:
 * - Fixed compact header (50px)
 * - Task selector dropdown
 * - No test infrastructure
 *
 * Usage:
 *   require_once __DIR__.'/layout.php';
 *   $content = '...HTML...';
 *   renderDemoLayout($content, $taskName, $bodyClass);
 */

require_once dirname(__DIR__, 2) . '/shared/helpers/dev_reload.php';

/**
 * Renders the full demo page layout with fixed header
 *
 * @param string $content HTML content for the page
 * @param string $taskName Task name for title (e.g., "Завдання 1")
 * @param string $bodyClass CSS class for body (e.g., "task1-body")
 */
function renderDemoLayout(string $content, string $taskName, string $bodyClass = ''): void
{
    $currentTask = basename($_SERVER['SCRIPT_NAME']);

    // Demo tasks list for selector
    $demoTasks = [
        'task1.php' => 'Завдання 1',
        'task2.php' => 'Завдання 2',
        'task3.php' => 'Завдання 3',
        'task4.php' => 'Завдання 4',
    ];
    ?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($taskName) ?> — Демо ЛР3</title>
    <link rel="stylesheet" href="demo.css">
</head>

<body class="body-with-header <?= htmlspecialchars($bodyClass) ?>">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
            <a href="index.php" class="header-btn">← Демо</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            <span class="header-variant-label">Демо ЛР3</span>
            <select class="header-task-select" onchange="if(this.value) location.href=this.value">
                <?php foreach ($demoTasks as $file => $name): ?>
                <option value="<?= htmlspecialchars($file) ?>"
                    <?= $file === $currentTask ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </header>

    <div class="content-wrapper">
        <?= $content ?>
    </div>

    <?= devReloadScript() ?>
</body>

</html>
<?php
}
