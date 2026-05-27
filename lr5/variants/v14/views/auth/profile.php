<?php
$user = $user ?? [];
$items = $items ?? [];
$type = $type ?? 'anime'; // anime | manga
$status = $status ?? 'planning'; // planning | watching | watched
$favorites = $favorites ?? [];

$avatar = !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png';
$displayName = trim($user['display_name'] ?? '') ?: trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$displayName = $displayName !== '' ? $displayName : ($user['login'] ?? '');
?>

<div style="max-width:1400px;margin:0 auto">
    <h1 style="margin-bottom: 24px;">Мій профіль</h1>
    
    <!-- Profile Header Card -->
    <div class="card" style="text-align: center; padding: 24px; margin-bottom: 32px; background: linear-gradient(135deg, #0a1420, #061022);">
        <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 24px; align-items: center;">
            <img class="profile-avatar" src="<?= $avatar ?>" alt="Avatar" 
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(37, 99, 235, 0.3);"
                 onerror="this.onerror=null;this.src='https://via.placeholder.com/120?text=No+Avatar'">
            <div style="text-align: left;">
                <h2 style="margin: 0 0 8px 0; font-size: 1.5rem;"><?= htmlspecialchars($displayName) ?></h2>
                <div class="profile-login">@<?= htmlspecialchars($user['login'] ?? '') ?></div>
                <p style="color: var(--muted); font-size: 0.85rem; margin: 8px 0 0 0;"><?= htmlspecialchars($user['email'] ?? '') ?></p>
            </div>
            <a href="index.php?route=settings/profile" class="btn" style="text-align: center; white-space: nowrap;">✏️ Редагувати</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
        <!-- Full Width Column -->
        <div>

        <!-- Right Column -->
        <div>
            <!-- Filter Panel -->
            <div class="card" style="margin-bottom: 24px;">
                <h3 style="margin: 0 0 16px 0;">Мої списки</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                    <a href="index.php?route=auth/profile&type=anime&status=<?= $status ?>" class="btn" style="text-align: center; padding: 10px; <?= $type === 'anime' ? 'background: #2563eb' : 'background: #334155' ?>">📺 Аніме</a>
                    <a href="index.php?route=auth/profile&type=manga&status=<?= $status ?>" class="btn" style="text-align: center; padding: 10px; <?= $type === 'manga' ? 'background: #2563eb' : 'background: #334155' ?>">📖 Манга</a>
                </div>
                <label style="color: var(--muted); font-size: 0.85rem; text-transform: uppercase; display: block; margin-bottom: 8px;">Статус</label>
                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px;">
                    <a href="index.php?route=auth/profile&type=<?= $type ?>&status=planning" class="btn" style="text-align: left; padding: 8px 12px; <?= $status === 'planning' ? 'background: #2563eb' : 'background: #334155' ?>">📋 Заплановано</a>
                    <a href="index.php?route=auth/profile&type=<?= $type ?>&status=watching" class="btn" style="text-align: left; padding: 8px 12px; <?= $status === 'watching' ? 'background: #2563eb' : 'background: #334155' ?>">👀 Дивлюсь / Читаю</a>
                    <a href="index.php?route=auth/profile&type=<?= $type ?>&status=watched" class="btn" style="text-align: left; padding: 8px 12px; <?= $status === 'watched' ? 'background: #2563eb' : 'background: #334155' ?>">✓ Завершено</a>
                </div>

                <!-- Favorites Card Nested -->
                <div style="border-top: 1px solid rgba(255,255,255,0.03); padding-top: 16px;">
                    <h3 style="margin: 0 0 12px 0; font-size: 0.95rem;">♡ Улюблені</h3>
                    <div class="favorites-list" style="max-height: 250px; overflow-y: auto;">
                        <?php if (!empty($favorites)): ?>
                            <?php foreach ($favorites as $fav): ?>
                                <a href="index.php?route=<?= ($fav['type'] ?? 'anime') === 'manga' ? 'manga' : 'anime' ?>/view&id=<?= $fav['id'] ?>" class="favorites-item" style="padding: 8px; margin: 0;">
                                    <?php
                                        $cover = !empty($fav['cover_url']) ? htmlspecialchars($fav['cover_url']) : 'https://via.placeholder.com/48x72?text=No+Cover';
                                    ?>
                                    <img src="<?= $cover ?>" alt="" class="favorites-item-img" onerror="this.onerror=null;this.src='https://via.placeholder.com/48x72?text=No+Cover'">
                                    <div class="favorites-item-info">
                                        <h4 class="favorites-item-title" style="font-size: 0.9rem;"><?= htmlspecialchars($fav['title_ua'] ?: $fav['title']) ?></h4>
                                        <p class="favorites-item-meta" style="font-size: 0.75rem;"><?= htmlspecialchars($fav['year'] ?? '') ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="margin: 0; color: var(--muted); font-size: 0.85rem; text-align: center; padding: 12px;">Немає улюблених</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Items Grid -->
            <div>
                <h2 style="margin-top: 0; margin-bottom: 16px;"><?= ($type === 'manga' ? '📖 Манга' : '📺 Аніме') ?> — <?= $status === 'planning' ? 'Заплановано' : ($status === 'watching' ? 'Дивлюсь / Читаю' : 'Завершено') ?></h2>
                
                <div class="card-grid catalog-grid">
                    <?php 
                        if (!empty($items)):
                            foreach ($items as $item):
                    ?>
                        <div class="card card-catalog" style="position:relative">
                            <?php $cover = !empty($item['cover_url']) ? htmlspecialchars($item['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
                            <?php if ($type === 'manga'): ?>
                                <a href="index.php?route=manga/view&id=<?= $item['id'] ?>"><img src="<?= $cover ?>" alt="" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                            <?php else: ?>
                                <a href="index.php?route=anime/view&id=<?= $item['id'] ?>"><img src="<?= $cover ?>" alt="" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                            <?php endif; ?>
                            <div style="padding-top:6px">
                                <h3 class="card__title"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></h3>
                                <p class="card__text" style="font-size:0.85rem;color:var(--muted)"><?= htmlspecialchars($item['year'] ?? '') ?> • <?= htmlspecialchars($item['type'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php 
                            endforeach;
                        else:
                    ?>
                        <p style="grid-column:1/-1;text-align:center;color:var(--muted);padding:40px 0">Поки що нічого немає</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
