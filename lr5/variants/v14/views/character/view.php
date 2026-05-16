<?php
$item = $item ?? null;
$anime = $anime ?? [];
$manga = $manga ?? [];
?>

<div class="page">
    <?php if (!$item): ?>
        <p>Персонаж не знайдений.</p>
    <?php else: ?>
        <div class="modal-detail">
            <div class="left">
                <?php $cimg = !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/300x420?text=No+Image'; ?>
                <img src="<?= $cimg ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>" class="character-main-img" onerror="this.onerror=null;this.src='https://via.placeholder.com/300x420?text=No+Image'">
                <div style="margin-top:12px;color:var(--muted)">
                    <div><strong>Ім'я:</strong> <?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></div>
                    <div><strong>Стать:</strong> <?= htmlspecialchars($item['gender'] ?? '') ?></div>
                    <div><strong>Вік:</strong> <?= htmlspecialchars($item['age'] ?? '') ?></div>
                </div>
            </div>
            <div class="right">
                <div style="display:flex;gap:12px;align-items:center;justify-content:space-between">
                    <h2 style="margin-top:0"><?= htmlspecialchars($item['name_ua'] ?: $item['name']) ?></h2>
                    <?php
                        $canEditChar = false;
                        if (isset($_SESSION['user_id'])) {
                            try {
                                $db = Database::getInstance();
                                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                                $rs->execute([':id' => $_SESSION['user_id']]);
                                $r = $rs->fetch();
                                $canEditChar = $r && ($r['role'] === 'admin');
                            } catch (Exception $e) { $canEditChar = false; }
                        }
                    ?>
                    <?php if ($canEditChar): ?>
                        <div><a class="btn btn--small" href="index.php?route=character/edit&id=<?= $item['id'] ?>">Редагувати</a></div>
                    <?php endif; ?>
                </div>
                <p style="color:var(--muted);line-height:1.5"><?= nl2br(htmlspecialchars($item['description'] ?? '')) ?></p>

                <?php if (!empty($anime)): ?>
                    <h3>Аніме, де фігурує персонаж</h3>
                    <div class="related-grid">
                        <?php foreach ($anime as $a): ?>
                            <a class="character-card" href="index.php?route=anime/view&id=<?= $a['id'] ?>">
                                <?php $ac = !empty($a['cover_url']) ? htmlspecialchars($a['cover_url']) : 'https://via.placeholder.com/240x160?text=No+Cover'; ?>
                                <img src="<?= $ac ?>" alt="" class="related-thumb" onerror="this.onerror=null;this.src='https://via.placeholder.com/240x160?text=No+Cover'">
                                <div class="name"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($manga)): ?>
                    <h3>Манга, де фігурує персонаж</h3>
                    <div class="related-grid">
                        <?php foreach ($manga as $m): ?>
                            <a class="character-card" href="index.php?route=manga/view&id=<?= $m['id'] ?>">
                                <?php $mc = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : 'https://via.placeholder.com/240x160?text=No+Cover'; ?>
                                <img src="<?= $mc ?>" alt="" class="related-thumb" onerror="this.onerror=null;this.src='https://via.placeholder.com/240x160?text=No+Cover'">
                                <div class="name"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div style="clear:both"></div>
        </div>
    <?php endif; ?>
</div>
