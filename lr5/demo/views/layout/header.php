<?php
$bgColor = $_SESSION['bg_color'] ?? '#f9fafb';
$greetingName = $_COOKIE['greeting_name'] ?? '';
$greetingGender = $_COOKIE['greeting_gender'] ?? '';

$greetingText = '';
if ($greetingName !== '') {
    $title = $greetingGender === 'female' ? 'пані' : 'пане';
    $greetingText = "Вітаємо Вас, {$title} " . htmlspecialchars($greetingName) . "!";
}

$isLoggedIn = isset($_SESSION['user_id']);
$userLogin = $_SESSION['user_login'] ?? '';

$currentRoute = $_GET['route'] ?? 'index/main';

$navItems = [
    'index/main' => 'Головна',
    'guestbook/index' => 'Гостьова книга',
    'upload/index' => 'Завантаження',
    'folder/create' => 'Каталоги',
    'catalog/list' => 'Каталог товарів',
    'settings/color' => 'Налаштування',
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'MVC Додаток') ?> — PHP MVC Demo (LR5)</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: <?= htmlspecialchars($bgColor) ?>">
    <a href="#main-content" class="skip-link">Перейти до вмісту</a>
    <header class="header">
        <div class="container">
            <div class="header__inner">
                <a href="index.php" class="header__logo">PHP MVC Demo — LR5</a>
                <div class="header__right">
                    <?php if ($greetingText !== ''): ?>
                        <span class="header__greeting"><?= $greetingText ?></span>
                    <?php endif; ?>
                    <div class="header__auth">
                        <?php if ($isLoggedIn): ?>
                            <a href="index.php?route=auth/profile" class="header__auth-link"><?= htmlspecialchars($userLogin) ?></a>
                            <a href="index.php?route=auth/logout" class="header__auth-link header__auth-link--logout">Вийти</a>
                        <?php else: ?>
                            <a href="index.php?route=auth/login" class="header__auth-link">Увійти</a>
                            <a href="index.php?route=auth/register" class="header__auth-link">Реєстрація</a>
                        <?php endif; ?>
                    </div>
                </div>
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
    <main class="main" id="main-content">
        <div class="container">
            <?php
            if (!empty($_SESSION['flash_success'])):
                $flash = $_SESSION['flash_success'];
                unset($_SESSION['flash_success']);
            ?>
                <div class="alert alert--success" role="alert"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>
