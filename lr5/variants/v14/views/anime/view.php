<div class="page">
    <div class="detail-and-side anime-detail-wrapper">
        <div class="modal-detail">
            <div class="left">
                <?php $cover = !empty($item['cover_url']) ? htmlspecialchars($item['cover_url']) : (!empty($item['poster_url']) ? htmlspecialchars($item['poster_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'); ?>
                <img src="<?= $cover ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" style="width:100%;border-radius:8px" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'">

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <form method="post" action="index.php?route=rating/toggle_favorite" style="display:flex">
                            <input type="hidden" name="anime_id" value="<?= $item['id'] ?>">
                            <button class="btn" type="submit" style="flex:1"><?= (!empty($userRow) && !empty($userRow['is_favorite'])) ? '♥ В улюблених' : '♡ Додати' ?></button>
                        </form>

                        <form method="post" action="index.php?route=rating/set_status" style="display:flex;gap:4px;align-items:center">
                            <input type="hidden" name="anime_id" value="<?= $item['id'] ?>">
                            <select name="status" class="form__input" style="flex:1;font-size:0.9rem">
                                <option value="">Статус</option>
                                <option value="planning" <?= (!empty($userRow) && ($userRow['status'] ?? '') === 'planning') ? 'selected' : '' ?>>Заплановано</option>
                                <option value="watching" <?= (!empty($userRow) && ($userRow['status'] ?? '') === 'watching') ? 'selected' : '' ?>>Дивлюсь</option>
                                <option value="watched" <?= (!empty($userRow) && ($userRow['status'] ?? '') === 'watched') ? 'selected' : '' ?>>Завершено</option>
                            </select>
                            <button class="btn" type="submit" style="padding:8px 12px">✓</button>
                        </form>

                        <form method="post" action="index.php?route=rating/set_score" style="display:flex;gap:4px;align-items:center">
                            <input type="hidden" name="anime_id" value="<?= $item['id'] ?>">
                            <select name="score" class="form__input" style="flex:1;font-size:0.9rem">
                                <option value="">★ Оцініть</option>
                                <?php for ($s = 1; $s <= 10; $s++): ?>
                                    <option value="<?= $s ?>" <?= (!empty($userRow) && ((float)($userRow['score'] ?? 0) == $s)) ? 'selected' : '' ?>>★ <?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                            <button class="btn" type="submit" style="padding:8px 12px">✓</button>
                        </form>
                    </div>
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
                                $canEdit = $r && in_array($r['role'], ['admin','moderator']);
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

                <div class="info-block" style="display:flex;gap:12px;flex-wrap:wrap;margin-top:0">
                    <div style="width:100%">
                        <h3>Деталі</h3>
                        <table class="table" style="max-width:100%;font-size:0.95rem">
                            <tr><td style="width:40%">Джерело</td><td><?= htmlspecialchars($item['source'] ?? '') ?></td></tr>
                            <tr><td>Тривалість епізоду</td>
                                <td>
                                    <?php
                                        $ep = isset($item['episode_duration']) ? (int)$item['episode_duration'] : 0;
                                        if ($ep > 0) {
                                            echo htmlspecialchars($ep) . ' хв';
                                        } else {
                                            $type = strtolower(trim($item['type'] ?? ''));
                                            $fallback = null;
                                            if (strpos($type, 'tv') !== false || $type === 'tv') $fallback = 24;
                                            elseif ($type === 'movie') $fallback = 120;
                                            elseif (in_array($type, ['ova','ona'])) $fallback = 12;
                                            if ($fallback) echo $fallback . ' хв'; else echo 'N/A';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr><td>Рейтинг</td><td><strong>★ <?= round($avgRating ?? 0,2) ?>/10</strong></td></tr>
                            <tr><td>Перегляди</td><td><?= htmlspecialchars($item['views'] ?? 0) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
        </div> <!-- .modal-detail -->
    </div> <!-- .detail-and-side -->

    <div class="content-columns">
        <div class="main-column">
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
        </div>
    </div>

    <div class="detail-bottom">
        <?php if (!empty($characters)): ?>
            <h3>Головні Персонажі</h3>
            <div class="characters-grid">
                <?php foreach ($characters as $ch): ?>
                    <a class="character-card" href="index.php?route=character/view&id=<?= $ch['id'] ?>">
                        <?php if (!empty($ch['image_url'])): ?><img src="<?= htmlspecialchars($ch['image_url']) ?>" alt="<?= htmlspecialchars($ch['name'] ?? '') ?>" class="character-card-img" onerror="this.onerror=null;this.src='https://via.placeholder.com/180x240?text=No+Image'" /><?php endif; ?>
                        <div class="character-card-name"><?= htmlspecialchars($ch['name_ua'] ?: $ch['name']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 style="margin-top:28px">Коментарі</h2>
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
            <div class="no-comments">Поки що немає коментарів.</div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="index.php?route=guestbook/index" method="post" class="comment-form">
                <div class="form-group">
                    <label for="comment_anime" class="form__label">Додати коментар</label>
                    <textarea id="comment_anime" name="comment" rows="4" required class="form__textarea"></textarea>
                </div>
                <input type="hidden" name="item_type" value="anime">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                <div class="form__actions"><button class="btn btn-primary">Відправити</button></div>
            </form>
        <?php else: ?>
            <p>Щоб залишити коментар, <a href="index.php?route=auth/login">увійдіть</a>.</p>
        <?php endif; ?>

    </div>
</div>
