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
                <?php
                    $showEmail = false;
                    if (!empty($user['show_email']) && $user['show_email'] == '1') {
                        $showEmail = true;
                    } elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == ($user['id'] ?? 0)) {
                        $showEmail = true;
                    } else {
                        try {
                            if (isset($_SESSION['user_id'])) {
                                $db = Database::getInstance();
                                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                $rs->execute([':id' => $_SESSION['user_id']]);
                                $rr = $rs->fetch();
                                if ($rr && ($rr['role'] === 'admin')) $showEmail = true;
                            }
                        } catch (Exception $e) { /* ignore */ }
                    }
                ?>
                <tr><td><strong>E-mail</strong></td><td><?= $showEmail ? htmlspecialchars($user['email'] ?? '-') : '<span class="muted">Приховано</span>' ?></td></tr>
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
                <label>
                    <input type="checkbox" name="show_email" value="1" <?= (!empty($user['show_email']) && $user['show_email'] == '1') ? 'checked' : '' ?>> Показувати E-mail в профілі
                </label>
                <label>
                    <input type="checkbox" name="notify_comments" value="1" <?= (!empty($user['notify_comments']) && $user['notify_comments'] == '1') ? 'checked' : '' ?>> Отримувати сповіщення про відповіді/коментарі
                </label>
                <div class="form__actions"><button class="btn" type="submit">Зберегти</button></div>
            </form>

            <!-- DEV-акаунти видалено з інтерфейсу налаштувань -->
        </aside>
    </div>
</div>
