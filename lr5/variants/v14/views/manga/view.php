<div class="page">
    <h1><?= htmlspecialchars($item['title']) ?></h1>

    <div class="manga-detail">
        <?php if (!empty($item['cover_url'])): ?>
            <img src="<?= htmlspecialchars($item['cover_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="max-width:200px; float:right; margin-left:15px;">
        <?php endif; ?>
        <p><strong>Рік:</strong> <?= htmlspecialchars($item['year']) ?></p>
        <p><strong>Тип:</strong> <?= htmlspecialchars($item['type']) ?></p>
        <p><strong>Статус:</strong> <?= htmlspecialchars($item['status']) ?></p>
        <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
    </div>

    <?php if (!empty($comments)): ?>
        <h2>Коментарі</h2>
        <?php foreach ($comments as $c): ?>
            <div class="comment">
                <div class="comment-meta"><?= htmlspecialchars($c['login']) ?> — <?= htmlspecialchars($c['created_at']) ?></div>
                <div class="comment-body"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Поки що немає коментарів.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="index.php?route=guestbook/index" method="post">
            <h3>Додати коментар</h3>
            <input type="hidden" name="item_type" value="manga">
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
            <div class="form-group">
                <textarea name="comment" rows="4" required></textarea>
            </div>
            <button class="btn btn-primary">Відправити</button>
        </form>
    <?php else: ?>
        <p>Щоб залишити коментар, <a href="index.php?route=auth/login">увійдіть</a>.</p>
    <?php endif; ?>
</div>
