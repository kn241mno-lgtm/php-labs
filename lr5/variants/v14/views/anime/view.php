<div class="page">
    <div class="modal-detail">
        <div class="left">
            <?php if (!empty($item['cover_url'])): ?>
                <img src="<?= htmlspecialchars($item['cover_url']) ?>" alt="" style="width:100%;border-radius:8px">
            <?php endif; ?>
            <div style="margin-top:12px;font-weight:700;color:#ffd166;font-size:20px">★ <?= round($item['rating'] ?? 0,1) ?>/10</div>
        </div>
        <div class="right">
            <h2 style="margin-top:0"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></h2>
            <div style="display:flex;gap:24px;color:var(--muted)">
                <div><strong>Рік:</strong> <?= htmlspecialchars($item['year']) ?></div>
                <div><strong>Епізоди:</strong> <?= htmlspecialchars($item['episodes'] ?? '') ?></div>
                <div><strong>Тип:</strong> <?= htmlspecialchars($item['type']) ?></div>
            </div>
            <div class="genres">
                <?php foreach (($genres ?? []) as $g): ?>
                    <span class="genre-chip"><?= htmlspecialchars($g['name']) ?></span>
                <?php endforeach; ?>
            </div>
            <h3 style="margin-top:18px">Опис</h3>
            <p style="color:var(--muted)"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></p>
        </div>
        <div style="clear:both"></div>
    </div>

    <?php if (!empty($comments)): ?>
        <h2>Коментарі</h2>
        <?php foreach ($comments as $c): ?>
            <div class="comment">
                <div class="comment-meta"><?= htmlspecialchars($c['login']) ?> — <?= htmlspecialchars($c['created_at']) ?></div>
                <div class="comment-body"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                        // show delete for admin
                        $isAdmin = false;
                        if (isset($_SESSION['user_id']) && isset($this)) {
                            try {
                                $db = Database::getInstance();
                                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                $rs->execute([':id' => $_SESSION['user_id']]);
                                $r = $rs->fetch();
                                $isAdmin = $r && ($r['role'] === 'admin');
                            } catch (Exception $e) {
                                $isAdmin = false;
                            }
                        }
                    ?>
                    <?php if ($isAdmin): ?>
                        <form method="post" action="index.php?route=guestbook/delete&id=<?= $c['id'] ?>" style="display:inline">
                            <button class="btn btn-small" onclick="return confirm('Видалити коментар?')">Видалити</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Поки що немає коментарів.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="index.php?route=guestbook/index" method="post">
            <h3>Додати коментар</h3>
            <input type="hidden" name="item_type" value="anime">
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
