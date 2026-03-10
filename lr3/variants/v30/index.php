<?php
/**
 * Variant 30 Index Page — LR3 (OOP)
 */

require_once dirname(__DIR__, 3) . '/shared/templates/task_cards.php';
require_once dirname(__DIR__, 3) . '/shared/helpers/paths.php';

$tasks = [
    'task1.php' => ['name' => 'Завдання 1'],
    'task2.php' => ['name' => 'Завдання 2'],
    'task3.php' => ['name' => 'Завдання 3'],
    'task4.php' => ['name' => 'Завдання 4'],
];

$demoUrl = '/lr3/demo/index.php?from=v30';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Варіант 30 — ЛР3</title>
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 3) . '/shared/css/base.css') ?>">
    <link rel="stylesheet" href="<?= webPath(dirname(__DIR__, 2) . '/demo/demo.css') ?>">
</head>
<body class="index-page">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            Варіант 30 ЛР3
        </div>
    </header>

    <h1 class="index-title">
        Варіант 30
        <br><span class="index-subtitle">ООП: класи, об'єкти, конструктори, клонування</span>
    </h1>

    <?= renderTaskCards($tasks, true, $demoUrl) ?>
</body>
</html>
