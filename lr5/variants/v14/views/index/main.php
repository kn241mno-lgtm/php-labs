<div class="page-home">
    <h1>Головна</h1>

    <section>
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:center">
            <h2 style="margin:0">Онгойнги</h2>
            <a class="section-link" href="index.php?route=anime/list" style="color:var(--muted);text-decoration:none">Переглянути каталог →</a>
        </div>
        <div class="card-grid card-grid--big">
            <?php foreach (($ongoings ?? []) as $a): ?>
                <a href="index.php?route=anime/view&id=<?= $a['id'] ?>" style="text-decoration:none;color:inherit">
                <div class="card">
                    <?php if (!empty($a['cover_url'])): ?>
                        <img src="<?= htmlspecialchars($a['cover_url']) ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="card__img" style="margin-bottom:8px">
                    <?php endif; ?>
                    <h3 class="card__title"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></h3>
                    <div class="card__text"><?= htmlspecialchars(mb_substr($a['description'] ?? $a['title'],0,120)) ?></div>
                    <div style="margin-top:8px;color:var(--muted);font-size:13px"><?= htmlspecialchars($a['studio_name'] ?? '') ?> • <?= htmlspecialchars($a['year'] ?? '') ?></div>
                </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:center">
            <h2 style="margin:0">Останні новини</h2>
            <a class="section-link" href="index.php?route=recipe/list" style="color:var(--muted);text-decoration:none">Всі новини →</a>
        </div>
        <div class="card-grid">
            <?php foreach (($news ?? []) as $n): ?>
                <a href="index.php?route=recipe/view&id=<?= $n['id'] ?>" style="text-decoration:none;color:inherit">
                <div class="card">
                    <?php if (!empty($n['image_url'])): ?>
                        <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="<?= htmlspecialchars($n['title']) ?>" class="card__img" style="margin-bottom:8px">
                    <?php endif; ?>
                    <h3 class="card__title"><?= htmlspecialchars($n['title']) ?></h3>
                    <div class="card__text"><?= htmlspecialchars(mb_substr($n['summary'] ?? $n['content'] ?? '',0,140)) ?></div>
                    <div style="margin-top:8px;color:var(--muted);font-size:13px">Автор: <?= htmlspecialchars($n['author'] ?? 'Адмін') ?> • <?= htmlspecialchars($n['published_at'] ?? '') ?></div>
                </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:center">
            <h2 style="margin:0">Топ аніме</h2>
            <a class="section-link" href="index.php?route=anime/list" style="color:var(--muted);text-decoration:none">Переглянути каталог</a>
        </div>
        <div class="card-grid" style="grid-template-columns:repeat(5, minmax(160px, 1fr));">
            <?php foreach (($top ?? []) as $t): ?>
                <a href="index.php?route=anime/view&id=<?= $t['id'] ?>" style="text-decoration:none;color:inherit">
                    <div class="card">
                        <?php if (!empty($t['cover_url'])): ?>
                            <img src="<?= htmlspecialchars($t['cover_url']) ?>" alt="<?= htmlspecialchars($t['title']) ?>" class="card__img" />
                        <?php endif; ?>
                        <h3 class="card__title"><?= htmlspecialchars($t['title_ua'] ?: $t['title']) ?></h3>
                        <div class="card__text" style="display:flex;justify-content:space-between;align-items:center"><span style="color:var(--muted)"><?= htmlspecialchars($t['studio_name'] ?? '') ?></span><span style="background:#1f2937;padding:6px 8px;border-radius:8px;color:#ffd166;font-weight:600"><?= round($t['rating'],1) ?></span></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>
