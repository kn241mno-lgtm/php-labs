<?php
$item = $item ?? null;
$comments = $comments ?? [];
?>

<div class="page">
    <?php if (!$item): ?>
        <p>Новина не знайдена.</p>
    <?php else: ?>
        <div class="card">
            <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
                <?php if (!empty($item['image_url'])): ?>
                    <div style="flex:0 0 320px"><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:6px"></div>
                <?php endif; ?>
                <div style="flex:1">
                    <h1 style="margin-top:0;margin-bottom:6px"><?= htmlspecialchars($item['title']) ?></h1>
                    <div style="color:var(--muted);margin-bottom:8px"> Автор: <?= htmlspecialchars($item['author'] ?? 'Адмін') ?> • <?= htmlspecialchars($item['published_at'] ?? $item['created_at']) ?></div>
                    <div class="news-content" style="margin-top:6px;color:var(--muted)"><?= nl2br(htmlspecialchars($item['content'] ?? '')) ?></div>
                </div>
            </div>
        </div>

        <hr>
        <div style="display:flex;justify-content:space-between;align-items:center">
            <h3>Коментарі</h3>
            <?php
                $canEditNews = false;
                if (isset($_SESSION['user_id'])) {
                    try {
                        $db = Database::getInstance();
                        $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                        $rs->execute([':id' => $_SESSION['user_id']]);
                        $r = $rs->fetch();
                        if ($r && in_array($r['role'], ['admin','helper'])) $canEditNews = true;
                    } catch (Exception $e) { $canEditNews = false; }
                }
            ?>
            <?php if ($canEditNews): ?>
                <a class="btn btn--small" href="index.php?route=recipe/edit&id=<?= $item['id'] ?>">Редагувати новину</a>
            <?php endif; ?>
        </div>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03)">
                    <div style="font-size:13px;color:#9fb0c9"><?= htmlspecialchars($c['login'] ?? 'Анонім') ?> • <?= htmlspecialchars($c['created_at']) ?></div>
                    <div style="margin-top:6px"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card comment-card">
                <div class="text-muted">Поки що немає коментарів.</div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="index.php?route=guestbook/index" method="post">
                <input type="hidden" name="item_type" value="news">
                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                <div class="form-group">
                    <textarea name="comment" rows="4" required></textarea>
                </div>
                <button class="btn btn-primary">Додати коментар</button>
            </form>
        <?php else: ?>
            <p>Щоб залишити коментар, <a href="index.php?route=auth/login">увійдіть</a>.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
