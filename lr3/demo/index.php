<?php
/**
 * Demo Index Page — ЛР3 (ООП)
 * Показує картки завдань
 */

require_once dirname(__DIR__, 2) . '/shared/templates/task_cards.php';

$tasks = [
    "task1.php" => ['name' => 'Завдання 1'],
    "task2.php" => ['name' => 'Завдання 2'],
    "task3.php" => ['name' => 'Завдання 3'],
    "task4.php" => ['name' => 'Завдання 4'],
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Демо — ЛР3</title>
    <link rel="stylesheet" href="demo.css">
</head>
<body class="index-page">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            Демо
        </div>
    </header>

    <h1 class="index-title">
        Демо ЛР3
        <br><span class="index-subtitle">ООП: класи, об'єкти, конструктори, клонування</span>
    </h1>

    <?= renderTaskCards($tasks, false, '') ?>
</body>
</html>
