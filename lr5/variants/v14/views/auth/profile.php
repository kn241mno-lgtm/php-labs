<?php
$user = $user ?? [];
$items = $items ?? [];
$type = $type ?? 'anime'; // anime | manga
$status = $status ?? 'planning'; // planning | watching | watched

$avatar = !empty($user['avatar_url']) ? htmlspecialchars($user['avatar_url']) : 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png';
$displayName = trim($user['display_name'] ?? '') ?: trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')));
$displayName = $displayName !== '' ? $displayName : ($user['login'] ?? '');
?>

<div class="profile-page" style="max-width:1200px;margin:0 auto;display:flex;gap:32px;flex-wrap:wrap">
    <div style="flex:0 0 300px">
        <div class="profile-header" style="display:flex;flex-direction:column;gap:16px;margin-bottom:24px">
            <img class="profile-avatar" src="<?= $avatar ?>" alt="Avatar" style="width:160px;height:160px;border-radius:8px;object-fit:cover" onerror="this.onerror=null;this.src='https://via.placeholder.com/160?text=No+Avatar'">
            <div>
                <h1 style="margin:0"><?= htmlspecialchars($displayName) ?></h1>
                <div class="profile-login" style="color:var(--muted);font-size:0.9rem;margin-top:4px">@<?= htmlspecialchars($user['login'] ?? '') ?></div>
            </div>
            <a href="index.php?route=settings/profile" class="btn" style="width:100%;text-align:center">Налаштування</a>
        </div>

        <div style="background:#0c1724;border:1px solid rgba(255,255,255,0.03);border-radius:8px;padding:16px">
            <h3 style="margin:0 0 12px 0;font-size:1rem">Мої списки</h3>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div>
                    <label style="color:var(--muted);font-size:0.85rem;text-transform:uppercase">Тип</label>
                    <div style="display:flex;gap:8px;margin-top:6px">
                        <a href="index.php?route=auth/profile&type=anime&status=<?= $status ?>" class="btn" style="flex:1;text-align:center;<?= $type === 'anime' ? 'background:#2563eb' : 'background:#334155' ?>">Аніме</a>
                        <a href="index.php?route=auth/profile&type=manga&status=<?= $status ?>" class="btn" style="flex:1;text-align:center;<?= $type === 'manga' ? 'background:#2563eb' : 'background:#334155' ?>">Манга</a>
                    </div>
                </div>
                <div>
                    <label style="color:var(--muted);font-size:0.85rem;text-transform:uppercase">Статус</label>
                    <div style="display:flex;flex-direction:column;gap:6px;margin-top:6px">
                        <a href="index.php?route=auth/profile&type=<?= $type ?>&status=planning" class="btn" style="text-align:left;padding:8px 12px;<?= $status === 'planning' ? 'background:#2563eb' : 'background:#334155' ?>">📋 Заплановано</a>
                        <a href="index.php?route=auth/profile&type=<?= $type ?>&status=watching" class="btn" style="text-align:left;padding:8px 12px;<?= $status === 'watching' ? 'background:#2563eb' : 'background:#334155' ?>">👀 Дивлюсь / Читаю</a>
                        <a href="index.php?route=auth/profile&type=<?= $type ?>&status=watched" class="btn" style="text-align:left;padding:8px 12px;<?= $status === 'watched' ? 'background:#2563eb' : 'background:#334155' ?>">✓ Завершено</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="flex:1;min-width:300px">
        <h2 style="margin-top:0"><?= ($type === 'manga' ? 'Манга' : 'Аніме') ?> — <?= $status === 'planning' ? 'Заплановано' : ($status === 'watching' ? 'Дивлюсь / Читаю' : 'Завершено') ?></h2>
        
        <div class="card-grid catalog-grid">
            <?php 
                if (!empty($items)):
                    foreach ($items as $item):
            ?>
                <div class="card">
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
