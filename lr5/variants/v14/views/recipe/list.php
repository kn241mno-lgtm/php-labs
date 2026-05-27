<?php $recipes = $recipes ?? []; ?>

<h1>Новини та статті</h1>

<?php
    $canManageNews = false;
    if (isset($_SESSION['user_id'])) {
        try {
            $db = Database::getInstance();
            $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
            $rs->execute([':id' => $_SESSION['user_id']]);
            $r = $rs->fetch();
            if ($r && in_array($r['role'], ['admin','moderator'])) $canManageNews = true;
        } catch (Exception $e) { $canManageNews = false; }
    }
?>
<div class="form__actions" style="margin-bottom: 20px">
    <?php if ($canManageNews): ?>
        <a href="index.php?route=recipe/create" class="btn">Додати статтю</a>
    <?php endif; ?>
</div>

<?php if (empty($recipes)): ?>
    <p class="text-muted">Статей ще немає.</p>
<?php else: ?>
    <div class="card-grid news-grid">
        <?php $count = 0; foreach ($recipes as $r): if($count++ >= 12) break; ?>
            <?php $img = !empty($r['image_url']) ? htmlspecialchars($r['image_url']) : 'https://via.placeholder.com/420x240?text=No+Image'; ?>
            <div class="card" style="position:relative">
                <a href="index.php?route=recipe/view&id=<?= (int)$r['id'] ?>">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($r['title'] ?? '') ?>" class="card__img" />
                </a>
                <div style="padding-top:8px">
                    <h3 class="card__title"><?= htmlspecialchars($r['title'] ?? '') ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($r['summary'] ?? trim($r['content'] ?? ''),0,140)) ?></p>
                    <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
                        <span class="text-muted"><?php echo htmlspecialchars($r['category'] ?? ''); ?></span>
                        <span class="text-muted">•</span>
                        <span class="text-muted"><?= htmlspecialchars($r['published_at'] ?? '') ?></span>
                    </div>
                    <div style="margin-top:10px">
                        <a href="index.php?route=recipe/view&id=<?= (int)$r['id'] ?>" class="btn btn--small">Читати</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
