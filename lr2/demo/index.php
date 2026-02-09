<?php
/**
 * Demo Index Page — LR2
 * Shows task cards for all 11 tasks
 */

require_once dirname(__DIR__, 2) . '/shared/templates/task_cards.php';

$tasks = [
    "task1_replace.php" => ['name' => 'Завдання 1'],
    "task2_sort.php" => ['name' => 'Завдання 2'],
    "task3_filename.php" => ['name' => 'Завдання 3'],
    "task4_dates.php" => ['name' => 'Завдання 4'],
    "task5_password.php" => ['name' => 'Завдання 5'],
    "task6_duplicates.php" => ['name' => 'Завдання 6'],
    "task7_animals.php" => ['name' => 'Завдання 7'],
    "task8_arrays.php" => ['name' => 'Завдання 8'],
    "task9_assoc.php" => ['name' => 'Завдання 9'],
    "task10_form.php" => ['name' => 'Завдання 10'],
    "task11_calc.php" => ['name' => 'Завдання 11'],
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Демо — ЛР2</title>
    <link rel="stylesheet" href="demo.css">
</head>
<body class="index-page">
    <header class="header-fixed">
        <div class="header-left">
            <a href="/" class="header-btn">Головна</a>
        </div>
        <div class="header-center"></div>
        <div class="header-right">
            Демо ЛР2
        </div>
    </header>

    <h1 class="index-title">
        Демо ЛР2
        <br><span class="index-subtitle">Функції, рядки, масиви, форми</span>
    </h1>

    <?= renderTaskCards($tasks, false, '') ?>
</body>
</html>
