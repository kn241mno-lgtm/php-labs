<?php
/**
 * Shared layout template for LR1 Variant 14 task pages
 */

require_once dirname(__DIR__, 3) . '/shared/helpers/dev_reload.php';
require_once dirname(__DIR__, 3) . '/shared/helpers/paths.php';

function renderVariantLayout(string $content, string $taskName, string $bodyClass = ''): void
{
    $currentTask = basename($_SERVER['SCRIPT_NAME']);

    $variantTasks = [
        'task2.php' => 'Завдання 1',
        'task3.php' => 'Завдання 2',
        'task4.php' => 'Завдання 3',
        'task5.php' => 'Завдання 4',
        'task6.php' => 'Завдання 5',
        'task7_table.php' => 'Завдання 6.1',
        'task7_squares.php' => 'Завдання 6.2',
    ];

    $demoUrl = "/lr1/demo/{$currentTask}?from=v14";
    ?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($taskName) ?> — Варіант 14 ЛР1</title>
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 3) . '/shared/css/base.css') ?>">
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 2) . '/demo/demo.css') ?>">
</head>

<body class="body-with-header <?= htmlspecialchars($bodyClass) ?>">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
            <a href="index.php" class="header-btn">← Варіант 14</a>
            <a href="<?= htmlspecialchars($demoUrl) ?>" class="header-btn header-btn-demo">Demo</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            <span class="header-variant-label">В-14</span>
            <select class="header-task-select" onchange="if(this.value) location.href=this.value">
                <?php foreach ($variantTasks as $file => $name): ?>
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
