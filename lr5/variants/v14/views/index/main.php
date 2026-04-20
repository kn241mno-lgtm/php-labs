<div class="page-home">
    <h1>Головна</h1>

    <section>
        <h2>Онгойнги</h2>
        <div class="card-grid">
            <?php foreach (($ongoings ?? []) as $a): ?>
                <a href="index.php?route=anime/view&id=<?= $a['id'] ?>" style="text-decoration:none;color:inherit">
                <div class="card">
                    <?php if (!empty($a['cover_url'])): ?>
                        <img src="<?= htmlspecialchars($a['cover_url']) ?>" alt="<?= htmlspecialchars($a['title']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:6px;margin-bottom:8px">
                    <?php endif; ?>
                    <h3 class="card__title"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></h3>
                    <div class="card__text"><?= htmlspecialchars(mb_substr($a['title'],0,120)) ?></div>
                    <div style="margin-top:8px;color:var(--muted);font-size:13px"><?= htmlspecialchars($a['studio_name'] ?? '') ?> • <?= htmlspecialchars($a['year'] ?? '') ?></div>
                </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2>Останні новини</h2>
        <div class="card-grid">
            <?php foreach (($news ?? []) as $n): ?>
                <a href="index.php?route=recipe/view&id=<?= $n['id'] ?>" style="text-decoration:none;color:inherit">
                <div class="card">
                    <?php if (!empty($n['image_url'])): ?>
                        <img src="<?= htmlspecialchars($n['image_url']) ?>" alt="<?= htmlspecialchars($n['title']) ?>" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px">
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
        <h2>Топ аніме</h2>
        <div class="card-grid" style="grid-template-columns:1fr;max-width:520px">
            <ol style="padding-left:18px;color:var(--muted)">
                <?php foreach (($top ?? []) as $t): ?>
                    <li style="margin:8px 0;display:flex;justify-content:space-between;align-items:center">
                        <span><?= htmlspecialchars($t['title_ua'] ?: $t['title']) ?></span>
                        <span style="background:#1f2937;padding:6px 8px;border-radius:8px;color:#ffd166;font-weight:600"><?= round($t['rating'],1) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
</div>
