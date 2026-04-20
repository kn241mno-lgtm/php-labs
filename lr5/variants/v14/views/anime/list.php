<div class="page">
    <h1>Каталог аніме</h1>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?route=anime/create" class="btn btn-primary">Додати аніме</a>
    <?php endif; ?>

    <div class="layout-row">
        <aside class="filters">
            <form method="get" action="index.php">
                <input type="hidden" name="route" value="anime/list">
                <div class="form-group">
                    <label>Пошук</label>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($filters['q'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Жанр</label>
                    <select name="genre">
                        <option value="0">Всі жанри</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= (isset($filters['genre']) && $filters['genre'] == $g['id']) ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Студія</label>
                    <select name="studioId">
                        <option value="0">Всі студії</option>
                        <?php foreach ($studios as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (isset($filters['studioId']) && $filters['studioId'] == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Тип</label>
                    <select name="type">
                        <option value="">Всі типи</option>
                        <option value="TV" <?= (isset($filters['type']) && $filters['type']=='TV')?'selected':'' ?>>TV</option>
                        <option value="Movie" <?= (isset($filters['type']) && $filters['type']=='Movie')?'selected':'' ?>>Movie</option>
                        <option value="OVA" <?= (isset($filters['type']) && $filters['type']=='OVA')?'selected':'' ?>>OVA</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select name="status">
                        <option value="">Всі статуси</option>
                        <option value="Ongoing" <?= (isset($filters['status']) && $filters['status']=='Ongoing')?'selected':'' ?>>Ongoing</option>
                        <option value="Completed" <?= (isset($filters['status']) && $filters['status']=='Completed')?'selected':'' ?>>Completed</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                </div>
            </form>
        </aside>

        <section class="content">
            <div class="card-grid">
                <?php foreach ($anime as $a): ?>
            <div class="card" style="position:relative">
                <?php if (!empty($a['status']) && strtolower($a['status']) === 'ongoing'): ?>
                    <div class="badge">Виходить</div>
                <?php endif; ?>
                <?php if (!empty($a['cover_url'])): ?>
                    <a href="index.php?route=anime/view&id=<?= $a['id'] ?>"><img src="<?= htmlspecialchars($a['cover_url']) ?>" alt="" style="width:100%;height:320px;object-fit:cover;border-radius:6px;margin-bottom:10px"></a>
                <?php endif; ?>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($a['title_ua'] ?: $a['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($a['description'] ?? '',0,120)) ?></p>
                    <a href="index.php?route=anime/view&id=<?= $a['id'] ?>" class="btn btn--small">Деталі</a>
                </div>
                <div class="rating-pill"><?= round($a['rating'] ?? 0,1) ?></div>
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
                        <a href="index.php?route=anime/edit&id=<?= $a['id'] ?>" class="btn btn-small">Редагувати</a>
                        <form method="post" action="index.php?route=anime/delete&id=<?= $a['id'] ?>" style="display:inline">
                            <button class="btn btn-small" onclick="return confirm('Видалити аніме?')">Видалити</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
