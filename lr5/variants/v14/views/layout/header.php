<?php
$bgColor = $_SESSION['bg_color'] ?? '#0b1b2c';
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
    'anime/list' => 'Аніме',
    'manga/list' => 'Манга',
    'recipe/list' => 'Новини',
];
// compute readable text color based on background
$textColor = '#e6eef8';
if (preg_match('/^#([0-9a-fA-F]{6})$/', $bgColor, $m)) {
    [$r, $g, $b] = array_map('hexdec', str_split($m[1], 2));
    $brightness = ($r * 0.299) + ($g * 0.587) + ($b * 0.114);
    $textColor = $brightness > 186 ? '#06121a' : '#e6eef8';
}
// muted color (less prominent than main text)
$mutedColor = ($textColor === '#06121a') ? '#6b7280' : '#9fb0c9';

?>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Miks') ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: <?= htmlspecialchars($bgColor) ?>; color: <?= htmlspecialchars($textColor) ?>; --color-base: <?= htmlspecialchars($textColor) ?>; --muted: <?= htmlspecialchars($mutedColor) ?>">
    <a href="#main-content" class="skip-link">Перейти до вмісту</a>
    <header class="header">
        <div class="container">
            <div class="header__inner">
                <a href="index.php" class="header__logo">
                    <img src="assets/logo.svg" alt="Miks logo" class="header__logo-img">
                </a>
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
                            <a href="index.php?route=settings/color" class="header__auth-link">Налаштування</a>
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
