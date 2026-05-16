<?php
$user = $user ?? [];

$avatar = !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png';
$displayName = trim($user['display_name'] ?? '') ?: trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$displayName = $displayName !== '' ? $displayName : ($user['login'] ?? '');
?>

<div class="profile-page">
    <div class="profile-header">
        <img class="profile-avatar" src="<?= $avatar ?>" alt="Avatar" onerror="this.onerror=null;this.src='https://via.placeholder.com/160?text=No+Avatar'">
        <div class="profile-info">
            <h1><?= htmlspecialchars($displayName) ?></h1>
            <div class="profile-login">@<?= htmlspecialchars($user['login'] ?? '') ?></div>
            <div class="profile-meta"><span class="badge"><?= htmlspecialchars($user['role'] ?? 'user') ?></span>
                <span class="muted">Зареєстровано: <?= htmlspecialchars($user['created_at'] ?? '-') ?></span>
            </div>
            <p class="profile-about"><?= nl2br(htmlspecialchars($user['about'] ?? '')) ?></p>
            <div class="form__actions">
                <a href="index.php?route=auth/edit" class="btn">Редагувати</a>
                <a href="index.php?route=auth/logout" class="btn btn--secondary">Вийти</a>
            </div>
        </div>
    </div>

    <div class="profile-grid">
        <div class="profile-details card">
            <h2>Дані акаунту</h2>
            <table class="table">
                <tr><td><strong>Логін</strong></td><td><?= htmlspecialchars($user['login'] ?? '') ?></td></tr>
                <tr><td><strong>Ім'я</strong></td><td><?= htmlspecialchars($user['first_name'] ?? '-') ?></td></tr>
                <tr><td><strong>Прізвище</strong></td><td><?= htmlspecialchars($user['last_name'] ?? '-') ?></td></tr>
                <tr><td><strong>E-mail</strong></td><td><?= htmlspecialchars($user['email'] ?? '-') ?></td></tr>
                <tr><td><strong>Телефон</strong></td><td><?= htmlspecialchars($user['phone'] ?? '-') ?></td></tr>
                <tr><td><strong>Місто</strong></td><td><?= htmlspecialchars($user['city'] ?? '-') ?></td></tr>
                <tr><td><strong>Стать</strong></td><td><?= ($user['gender'] ?? '') === 'female' ? 'Жіноча' : (($user['gender'] ?? '') === 'male' ? 'Чоловіча' : '-') ?></td></tr>
                <tr><td><strong>Про себе</strong></td><td><?= htmlspecialchars($user['about'] ?? '-') ?></td></tr>
            </table>
        </div>

        <aside class="profile-settings card">
            <h2>Налаштування</h2>
            <form method="post" action="index.php?route=auth/profile" class="profile-settings-form">
                <label>Колір сайту: <input type="color" name="ui_color" value="<?= htmlspecialchars($user['ui_color'] ?? '#2563eb') ?>"></label>
                <label>Відображуване ім'я: <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>"></label>
                <label>URL аватарки: <input type="url" name="avatar_url" value="<?= htmlspecialchars($user['avatar_url'] ?? '') ?>"></label>
                <div class="form__actions"><button class="btn" type="submit">Зберегти</button></div>
            </form>

            <!-- DEV-акаунти видалено з інтерфейсу налаштувань -->
        </aside>
    </div>
</div>
