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
            <h2>Список Аніме/Манг</h2>
            <div class="watch-list-section">
                <div class="watch-list-category">
                    <h3>Планує дивитись (Аніме)</h3>
                    <div class="watch-list-items">
                        <?php 
                            $db = Database::getInstance();
                            $planning = $db->prepare('SELECT a.* FROM anime a JOIN rating r ON a.id = r.anime_id WHERE r.user_id = :uid AND r.status = :status LIMIT 10');
                            $planning->execute([':uid' => $user['id'], ':status' => 'planning']);
                            $items = $planning->fetchAll();
                            if (!empty($items)):
                                foreach ($items as $item):
                        ?>
                            <a href="index.php?route=anime/view&id=<?= $item['id'] ?>" class="list-item"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></a>
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <span class="muted">Ніяких</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="watch-list-category">
                    <h3>Дивиться (Аніме)</h3>
                    <div class="watch-list-items">
                        <?php 
                            $watching = $db->prepare('SELECT a.* FROM anime a JOIN rating r ON a.id = r.anime_id WHERE r.user_id = :uid AND r.status = :status LIMIT 10');
                            $watching->execute([':uid' => $user['id'], ':status' => 'watching']);
                            $items = $watching->fetchAll();
                            if (!empty($items)):
                                foreach ($items as $item):
                        ?>
                            <a href="index.php?route=anime/view&id=<?= $item['id'] ?>" class="list-item"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></a>
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <span class="muted">Ніяких</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="watch-list-category">
                    <h3>Подивився (Аніме)</h3>
                    <div class="watch-list-items">
                        <?php 
                            $watched = $db->prepare('SELECT a.* FROM anime a JOIN rating r ON a.id = r.anime_id WHERE r.user_id = :uid AND r.status = :status LIMIT 10');
                            $watched->execute([':uid' => $user['id'], ':status' => 'watched']);
                            $items = $watched->fetchAll();
                            if (!empty($items)):
                                foreach ($items as $item):
                        ?>
                            <a href="index.php?route=anime/view&id=<?= $item['id'] ?>" class="list-item"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></a>
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <span class="muted">Ніяких</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="watch-list-category">
                    <h3>Улюблені</h3>
                    <div class="watch-list-items">
                        <?php 
                            $favorites = $db->prepare('SELECT a.* FROM anime a JOIN rating r ON a.id = r.anime_id WHERE r.user_id = :uid AND r.is_favorite = 1 LIMIT 10');
                            $favorites->execute([':uid' => $user['id']]);
                            $items = $favorites->fetchAll();
                            if (!empty($items)):
                                foreach ($items as $item):
                        ?>
                            <a href="index.php?route=anime/view&id=<?= $item['id'] ?>" class="list-item"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></a>
                        <?php 
                                endforeach;
                            else:
                        ?>
                            <span class="muted">Ніяких</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
