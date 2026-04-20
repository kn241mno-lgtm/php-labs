<?php
$item = $item ?? null;
$comments = $comments ?? [];
?>

<div class="page">
    <?php if (!$item): ?>
        <p>Новина не знайдена.</p>
    <?php else: ?>
        <h1><?= htmlspecialchars($item['title']) ?></h1>
        <?php if (!empty($item['image_url'])): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" style="max-width:420px;width:100%;border-radius:8px;margin-bottom:12px">
        <?php endif; ?>
        <div style="color:var(--muted);margin-bottom:8px"> Автор: <?= htmlspecialchars($item['author'] ?? 'Адмін') ?> • <?= htmlspecialchars($item['published_at'] ?? $item['created_at']) ?></div>
        <div class="news-content" style="margin-top:12px;color:var(--muted)"><?= nl2br(htmlspecialchars($item['content'] ?? '')) ?></div>

        <hr>
        <h3>Коментарі</h3>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03)">
                    <div style="font-size:13px;color:#9fb0c9"><?= htmlspecialchars($c['login'] ?? 'Анонім') ?> • <?= htmlspecialchars($c['created_at']) ?></div>
                    <div style="margin-top:6px"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Поки що немає коментарів.</p>
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
