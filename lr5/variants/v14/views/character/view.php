<?php
$item = $item ?? null;
$anime = $anime ?? [];
$manga = $manga ?? [];
?>

<div class="page">
    <?php if (!$item): ?>
        <p>Персонаж не знайдений.</p>
    <?php else: ?>
        <div class="character-detail">
            <div class="character-detail__header">
                <div class="character-detail__image">
                    <?php $cimg = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/300x420?text=No+Image'; ?>
                    <img src="<?= $cimg ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>" class="character-main-img" onerror="this.onerror=null;this.src='https://via.placeholder.com/300x420?text=No+Image'">
                </div>

                <div class="character-detail__info">
                    <div class="character-detail__header-top">
                        <h1 class="character-detail__title"><?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></h1>
                        <?php
                            $canEditChar = false;
                            if (isset($_SESSION['user_id'])) {
                                try {
                                    $db = Database::getInstance();
                                    $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                    $rs->execute([':id' => $_SESSION['user_id']]);
                                    $r = $rs->fetch();
                                    $canEditChar = $r && in_array($r['role'], ['admin','moderator']);
                                } catch (Exception $e) { $canEditChar = false; }
                            }
                        ?>
                        <?php if ($canEditChar): ?>
                            <a class="btn btn--small" href="index.php?route=character/edit&id=<?= $item['id'] ?>">Редагувати</a>
                        <?php endif; ?>
                    </div>

                    <div class="character-detail__attributes">
                        <div class="character-attr">
                            <span class="character-attr__label">Ім'я:</span>
                            <span class="character-attr__value"><?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></span>
                        </div>
                        <div class="character-attr">
                            <span class="character-attr__label">Стать:</span>
                            <span class="character-attr__value"><?= htmlspecialchars($item['gender'] ?? '-') ?></span>
                        </div>
                        <div class="character-attr">
                            <span class="character-attr__label">Вік:</span>
                            <span class="character-attr__value"><?= htmlspecialchars($item['age'] ?? '-') ?></span>
                        </div>
                    </div>

                    <div class="character-detail__description">
                        <h3>Опис</h3>
                        <p><?= nl2br(htmlspecialchars($item['description'] ?? 'Опис відсутній')) ?></p>
                    </div>
                </div>
            </div>

            <div class="character-detail__content">
                <div class="character-content-columns">
                    <div class="character-column">
                        <?php if (!empty($anime)): ?>
                            <section class="character-detail__section">
                                <h2 class="character-detail__section-title">Аніме, де фігурує персонаж</h2>
                                <div class="related-grid">
                                    <?php foreach ($anime as $a): ?>
                                        <a class="related-item" href="index.php?route=anime/view&id=<?= $a['id'] ?>">
                                            <?php $ac = !empty($a['cover_url']) ? htmlspecialchars($a['cover_url']) : 'https://via.placeholder.com/240x160?text=No+Cover'; ?>
                                            <img src="<?= $ac ?>" alt="" class="related-item__thumb" onerror="this.onerror=null;this.src='https://via.placeholder.com/240x160?text=No+Cover'">
                                            <div class="related-item__title"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php else: ?>
                            <section class="character-detail__section">
                                <h2 class="character-detail__section-title">Аніме, де фігурує персонаж</h2>
                                <div class="no-comments">Аніме з цим персонажем не знайдено.</div>
                            </section>
                        <?php endif; ?>
                    </div>

                    <div class="character-column">
                        <?php if (!empty($manga)): ?>
                            <section class="character-detail__section">
                                <h2 class="character-detail__section-title">Манга, де фігурує персонаж</h2>
                                <div class="related-grid">
                                    <?php foreach ($manga as $m): ?>
                                        <a class="related-item" href="index.php?route=manga/view&id=<?= $m['id'] ?>">
                                            <?php $mc = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : 'https://via.placeholder.com/240x160?text=No+Cover'; ?>
                                            <img src="<?= $mc ?>" alt="" class="related-item__thumb" onerror="this.onerror=null;this.src='https://via.placeholder.com/240x160?text=No+Cover'">
                                            <div class="related-item__title"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php else: ?>
                            <section class="character-detail__section">
                                <h2 class="character-detail__section-title">Манга, де фігурує персонаж</h2>
                                <div class="no-comments">Манг з цим персонажем не знайдено.</div>
                            </section>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
