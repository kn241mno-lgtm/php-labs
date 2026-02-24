<?php
/**
 * Shared layout template for LR2 Variant 30 task pages
 */

require_once dirname(__DIR__, 3) . '/shared/helpers/dev_reload.php';
require_once dirname(__DIR__, 3) . '/shared/helpers/paths.php';

function renderVariantLayout(string $content, string $taskName, string $bodyClass = ''): void
{
    $currentTask = basename($_SERVER['SCRIPT_NAME']);

    $variantTasks = [
        'task1_replace.php' => 'Завдання 1',
        'task2_sort.php' => 'Завдання 2',
        'task3_filename.php' => 'Завдання 3',
        'task4_dates.php' => 'Завдання 4',
        'task5_password.php' => 'Завдання 5',
        'task6_duplicates.php' => 'Завдання 6',
        'task7_animals.php' => 'Завдання 7',
        'task8_arrays.php' => 'Завдання 8',
        'task9_assoc.php' => 'Завдання 9',
        'task10_form.php' => 'Завдання 10',
        'task11_calc.php' => 'Завдання 11',
    ];

    $demoUrl = "/lr2/demo/{$currentTask}?from=v30";
    ?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($taskName) ?> — Варіант 30 ЛР2</title>
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 3) . '/shared/css/base.css') ?>">
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 2) . '/demo/demo.css') ?>">
</head>

<body class="body-with-header <?= htmlspecialchars($bodyClass) ?>">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
            <a href="index.php" class="header-btn">← Варіант 30</a>
            <a href="<?= htmlspecialchars($demoUrl) ?>" class="header-btn header-btn-demo">Demo</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            <span class="header-variant-label">В-30</span>
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
