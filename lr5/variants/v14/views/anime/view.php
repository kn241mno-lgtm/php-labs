<div class="page">
    <div class="modal-detail">
        <div class="left">
            <?php $cover = !empty($item['cover_url']) ? htmlspecialchars($item['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" style="width:100%;border-radius:8px" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'">
            <div style="margin-top:12px;font-weight:700;color:#ffd166;font-size:20px">★ <?= round($item['rating'] ?? 0,1) ?>/10</div>
            <?php if (!empty($studio ?? null)): ?>
                <div style="margin-top:14px;color:var(--muted)"><strong>Студія:</strong> <?= htmlspecialchars($studio['name']) ?></div>
            <?php endif; ?>
        </div>
        <div class="right">
            <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
                <h2 style="margin-top:0"><?= htmlspecialchars($item['title_ua'] ?: $item['title']) ?></h2>
                <?php
                    $canEdit = false;
                    if (isset($_SESSION['user_id'])) {
                        try {
                            $db = Database::getInstance();
                            $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                            $rs->execute([':id' => $_SESSION['user_id']]);
                            $r = $rs->fetch();
                            $canEdit = $r && ($r['role'] === 'admin');
                        } catch (Exception $e) { $canEdit = false; }
                    }
                ?>
                <?php if ($canEdit): ?>
                    <div><a class="btn btn--small" href="index.php?route=anime/edit&id=<?= $item['id'] ?>">Редагувати</a></div>
                <?php endif; ?>
            </div>
            <div style="display:flex;gap:20px;flex-wrap:wrap;color:var(--muted);margin-bottom:8px">
                <div><strong>Рік:</strong> <?= htmlspecialchars($item['year']) ?></div>
                <div><strong>Епізоди:</strong> <?= htmlspecialchars($item['episodes'] ?? '') ?></div>
                <div><strong>Тип:</strong> <?= htmlspecialchars($item['type']) ?></div>
                <div><strong>Статус:</strong> <?= htmlspecialchars($item['status'] ?? '') ?></div>
            </div>
            <div class="genres" aria-hidden="false">
                <?php foreach (($genres ?? []) as $g): ?>
                    <span class="genre-chip"><?= htmlspecialchars($g['name']) ?></span>
                <?php endforeach; ?>
            </div>
            <h3 style="margin-top:18px">Опис</h3>
            <p style="color:var(--muted);line-height:1.5"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></p>

            <div class="info-block" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:18px">
                <div style="min-width:160px">
                    <h3>Дії</h3>
                    <?php
                        $showActionButton = false;
                        if (isset($_SESSION['user_id'])) {
                            $showActionButton = true; // logged-in users can add favorites
                        }
                    ?>
                    <?php if ($showActionButton): ?>
                        <form method="post" action="index.php?route=rating/toggle_favorite">
                            <input type="hidden" name="anime_id" value="<?= $item['id'] ?>">
                            <button class="btn" type="submit">Додати в улюблені</button>
                        </form>
                    <?php else: ?>
                        <div class="no-comments">Увійдіть, щоб додати улюблене.</div>
                    <?php endif; ?>
                </div>
                <div style="flex:1">
                    <h3>Деталі</h3>
                    <table class="table" style="max-width:600px">
                        <tr><td>Джерело</td><td><?= htmlspecialchars($item['source'] ?? '') ?></td></tr>
                        <tr><td>Тривалість епізоду</td><td><?= htmlspecialchars($item['episode_duration'] ? $item['episode_duration'] . ' хв' : '') ?></td></tr>
                        <tr><td>Перегляди</td><td><?= htmlspecialchars($item['views'] ?? 0) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>

        <?php if (!empty($characters)): ?>
            <h3>Головні Персонажі</h3>
            <div class="related-grid">
                <?php foreach ($characters as $ch): ?>
                    <a class="character-card" href="index.php?route=character/view&id=<?= $ch['id'] ?>">
                        <?php if (!empty($ch['image_url'])): ?><img src="<?= htmlspecialchars($ch['image_url']) ?>" alt=""><?php endif; ?>
                        <div class="name"><?= htmlspecialchars($ch['name_ua'] ?: $ch['name']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($relatedManga)): ?>
            <h3>Пов'язані манга</h3>
            <div class="card-grid">
                <?php foreach ($relatedManga as $rm): ?>
                    <a href="index.php?route=manga/view&id=<?= $rm['id'] ?>" style="text-decoration:none;color:inherit">
                        <div class="card">
                            <?php if (!empty($rm['cover_url'])): ?>
                                <img src="<?= htmlspecialchars($rm['cover_url']) ?>" alt="" style="width:100%;height:120px;object-fit:cover;border-radius:6px;margin-bottom:8px">
                            <?php endif; ?>
                            <h3 class="card__title"><?= htmlspecialchars($rm['title_ua'] ?: $rm['title']) ?></h3>
                            <div class="card__text"><?= htmlspecialchars(mb_substr($rm['description'] ?? '',0,120)) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($comments)): ?>
        <h2>Коментарі</h2>
        <?php foreach ($comments as $c): ?>
            <div class="comment">
                <div class="comment-meta"><?= htmlspecialchars($c['login']) ?> — <?= htmlspecialchars($c['created_at']) ?></div>
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
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-comments">Поки що немає коментарів.</div>
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
