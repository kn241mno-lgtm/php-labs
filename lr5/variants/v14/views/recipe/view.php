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
                        <?php
                            $canEditNews = false;
                            if (isset($_SESSION['user_id'])) {
                                try {
                                    $db = Database::getInstance();
                                    $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                    $rs->execute([':id' => $_SESSION['user_id']]);
                                    $r = $rs->fetch();
                                    if ($r && in_array($r['role'], ['admin','moderator'])) $canEditNews = true;
                                } catch (Exception $e) { $canEditNews = false; }
                            }
                        ?>
                        <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
                            <h1 style="margin-top:0;margin-bottom:6px"><?= htmlspecialchars($item['title']) ?></h1>
                            <?php if ($canEditNews): ?>
                                <a class="btn btn--small" href="index.php?route=recipe/edit&id=<?= $item['id'] ?>">Редагувати</a>
                            <?php endif; ?>
                        </div>
                        <div style="color:var(--muted);margin-bottom:8px"> Автор: <?= htmlspecialchars($item['author'] ?? 'Адмін') ?> • <?= htmlspecialchars($item['published_at'] ?? $item['created_at']) ?></div>
                        <div class="news-content" style="margin-top:6px;color:var(--muted)"><?= nl2br(htmlspecialchars($item['content'] ?? '')) ?></div>
                    </div>
            </div>
        </div>

        <hr>
        <h3>Коментарі</h3>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                    <div class="comment">
                        <img class="avatar" src="<?= htmlspecialchars($c['avatar_url'] ?? '') ?>" alt="avatar" onerror="this.onerror=null;this.src='https://via.placeholder.com/48?text=U'">
                        <div class="comment-content">
                            <div class="comment-meta"><strong><?= htmlspecialchars($c['display_name'] ?: $c['login']) ?></strong> <span class="muted">— <?= htmlspecialchars($c['created_at']) ?></span></div>
                            <div class="comment-body"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php
                                    $isAdmin = false;
                                    try {
                                        $db = Database::getInstance();
                                        $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                        $rs->execute([':id' => $_SESSION['user_id']]);
                                        $r = $rs->fetch();
                                        $isAdmin = $r && ($r['role'] === 'admin');
                                    } catch (Exception $e) {
                                        $isAdmin = false;
                                    }
                                ?>
                                <?php if ($isAdmin): ?>
                                    <form method="post" action="index.php?route=guestbook/delete&id=<?= $c['id'] ?>" style="display:inline">
                                        <button class="btn btn-small" onclick="return confirm('Видалити коментар?')">Видалити</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card comment-card">
                <div class="text-muted">Поки що немає коментарів.</div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="index.php?route=guestbook/index" method="post" class="comment-form">
                <div class="form-group">
                    <label for="comment_news" class="form__label">Додати коментар</label>
                    <textarea id="comment_news" name="comment" rows="4" required class="form__textarea"></textarea>
                </div>
                <input type="hidden" name="item_type" value="news">
                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                <div class="form__actions"><button class="btn btn-primary">Додати коментар</button></div>
            </form>
        <?php else: ?>
            <p>Щоб залишити коментар, <a href="index.php?route=auth/login">увійдіть</a>.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
