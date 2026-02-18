<?php
/**
 * Variant 14 Index Page
 */

require_once dirname(__DIR__, 3) . '/shared/templates/task_cards.php';
require_once dirname(__DIR__, 3) . '/shared/helpers/paths.php';

$tasks = [
    'task2.php' => ['name' => 'Завдання 1'],
    'task3.php' => ['name' => 'Завдання 2'],
    'task4.php' => ['name' => 'Завдання 3'],
    'task5.php' => ['name' => 'Завдання 4'],
    'task6.php' => ['name' => 'Завдання 5'],
    'task7_table.php' => ['name' => 'Завдання 6.1'],
    'task7_squares.php' => ['name' => 'Завдання 6.2'],
];

$demoUrl = '/lr1/demo/index.php?from=v14';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Варіант 14 — ЛР1</title>
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
            Варіант 14
        </div>
    </header>

    <h1 class="index-title">
        Варіант 14
        <br><span class="index-subtitle">Лабораторна робота №1</span>
    </h1>

    <?= renderTaskCards($tasks, true, $demoUrl) ?>
</body>
</html>
