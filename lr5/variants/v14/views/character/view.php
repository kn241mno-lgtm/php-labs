<?php
$item = $item ?? null;
$anime = $anime ?? [];
$manga = $manga ?? [];
?>

<div class="page">
    <?php if (!$item): ?>
        <p>Персонаж не знайдений.</p>
    <?php else: ?>
        <div class="modal-detail">
            <div class="left">
                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>">
                <?php endif; ?>
                <div style="margin-top:12px;color:var(--muted)">
                    <div><strong>Ім'я:</strong> <?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></div>
                    <div><strong>Стать:</strong> <?= htmlspecialchars($item['gender'] ?? '') ?></div>
                    <div><strong>Вік:</strong> <?= htmlspecialchars($item['age'] ?? '') ?></div>
                </div>
            </div>
            <div class="right">
                <h2 style="margin-top:0"><?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></h2>
                <p style="color:var(--muted);line-height:1.5"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></p>

                <?php if (!empty($anime)): ?>
                    <h3>Аніме, де фігурує персонаж</h3>
                    <div class="related-grid">
                        <?php foreach ($anime as $a): ?>
                            <a class="character-card" href="index.php?route=anime/view&id=<?= $a['id'] ?>">
                                <?php if (!empty($a['cover_url'])): ?><img src="<?= htmlspecialchars($a['cover_url']) ?>" alt=""><?php endif; ?>
                                <div class="name"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($manga)): ?>
                    <h3>Манга, де фігурує персонаж</h3>
                    <div class="related-grid">
                        <?php foreach ($manga as $m): ?>
                            <a class="character-card" href="index.php?route=manga/view&id=<?= $m['id'] ?>">
                                <?php if (!empty($m['cover_url'])): ?><img src="<?= htmlspecialchars($m['cover_url']) ?>" alt=""><?php endif; ?>
                                <div class="name"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div style="clear:both"></div>
        </div>
    <?php endif; ?>
</div>
