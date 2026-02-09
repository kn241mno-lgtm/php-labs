<?php
$bgColor = $_SESSION['bg_color'] ?? '#f9fafb';
$greetingName = $_COOKIE['greeting_name'] ?? '';
$greetingGender = $_COOKIE['greeting_gender'] ?? '';

$greetingText = '';
if ($greetingName !== '') {
    $title = $greetingGender === 'female' ? 'пані' : 'пане';
    $greetingText = "Вітаємо Вас, {$title} " . htmlspecialchars($greetingName) . "!";
}

$currentRoute = $_GET['route'] ?? 'index/main';

$navItems = [
    'index/main' => 'Головна',
    'regform/form' => 'Реєстрація',
    'reqview/showrequest' => 'Параметри запиту',
    'settings/color' => 'Колір фону',
    'settings/greeting' => 'Привітання',
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'MVC Додаток') ?> — PHP MVC Demo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: <?= htmlspecialchars($bgColor) ?>">
    <header class="header">
        <div class="container">
            <div class="header__inner">
                <a href="index.php" class="header__logo">PHP MVC Demo</a>
                <?php if ($greetingText !== ''): ?>
                    <span class="header__greeting"><?= $greetingText ?></span>
                <?php endif; ?>
            </div>
            <nav class="nav">
                <ul class="nav__list">
                    <?php foreach ($navItems as $route => $label): ?>
                        <li class="nav__item">
                            <a href="index.php?route=<?= $route ?>"
                               class="nav__link<?= $currentRoute === $route ? ' nav__link--active' : '' ?>">
                                <?= htmlspecialchars($label) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main">
        <div class="container">
