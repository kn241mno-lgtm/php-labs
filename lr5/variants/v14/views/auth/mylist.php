<?php
$items = $items ?? [];
$type = $type ?? 'anime';
$status = $status ?? 'planning';
function statusLabel($s){
    switch($s){
        case 'planning': return 'Заплановано';
        case 'watching': return 'Дивлюсь';
        case 'watched': return 'Завершено';
    }
    return htmlspecialchars($s);
}
?>

<h1>Список: <?= $type === 'manga' ? 'Манга' : 'Аніме' ?> — <?= statusLabel($status) ?></h1>

<p><a href="index.php?route=auth/profile" class="btn btn--secondary">Повернутись до профілю</a></p>

<div class="card-grid catalog-grid">
    <?php if (empty($items)): ?>
        <p class="muted">Поки що нічого немає в цьому списку.</p>
    <?php else: foreach ($items as $it): ?>
        <div class="card" style="position:relative">
            <?php $cover = !empty($it['cover_url']) ? htmlspecialchars($it['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
            <?php if ($type === 'manga'): ?>
                <a href="index.php?route=manga/view&id=<?= $it['id'] ?>"><img src="<?= $cover ?>" alt="<?= htmlspecialchars($it['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
            <?php else: ?>
                <a href="index.php?route=anime/view&id=<?= $it['id'] ?>"><img src="<?= $cover ?>" alt="<?= htmlspecialchars($it['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
            <?php endif; ?>
            <div style="padding-top:6px">
                <h3 class="card__title"><?= htmlspecialchars($it['title_ua'] ?: $it['title']) ?></h3>
                <p class="card__text" style="font-size:0.85rem;color:var(--muted)"><?= htmlspecialchars($it['year'] ?? '') ?> • <?= htmlspecialchars($it['type'] ?? '') ?></p>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>
