<?php
$query = $query ?? '';
$results = $results ?? [];
?>

<div class="page">
    <h1>Результати пошуку</h1>

    <?php if ($query === ''): ?>
        <p>Введіть текст для пошуку.</p>
    <?php else: ?>
        <p>Знайдено <?= count($results) ?> результатів для «<?= htmlspecialchars($query) ?>»</p>

        <?php if (!empty($results)): ?>
            <div class="card-grid">
                <?php foreach ($results as $r): ?>
                    <?php
                        $type = $r['type'] ?? 'anime';
                        $link = 'index.php?route=anime/view&id=' . urlencode($r['id']);
                        if ($type === 'manga') $link = 'index.php?route=manga/view&id=' . urlencode($r['id']);
                        if ($type === 'character' || $type === 'character') $link = 'index.php?route=character/view&id=' . urlencode($r['id']);
                        $title = htmlspecialchars($r['title_ua'] ?: $r['title'] ?: '');
                        $img = !empty($r['image_url']) ? htmlspecialchars($r['image_url']) : 'https://via.placeholder.com/420x300?text=No+Image';
                    ?>
                    <a href="<?= $link ?>" style="text-decoration:none;color:inherit">
                        <div class="card" style="position:relative">
                            <img src="<?= $img ?>" alt="" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/240x160?text=No+Image'" />
                            <div style="padding-top:8px">
                                <h3 class="card__title"><?= $title ?></h3>
                                <div class="card__text" style="color:var(--muted)"><?= htmlspecialchars(ucfirst($type)) ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
