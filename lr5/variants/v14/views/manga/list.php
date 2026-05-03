<div class="page">
    <h1>Каталог манги</h1>
    <?php
        $canCreateManga = false;
        if (isset($_SESSION['user_id'])) {
            try {
                $db = Database::getInstance();
                $rs = $db->prepare('SELECT role FROM users WHERE id = :id');
                $rs->execute([':id' => $_SESSION['user_id']]);
                $r = $rs->fetch();
                $canCreateManga = $r && ($r['role'] === 'admin');
            } catch (Exception $e) { $canCreateManga = false; }
        }
    ?>
    <?php if ($canCreateManga): ?>
        <a href="index.php?route=manga/create" class="btn btn-primary">Додати мангу</a>
    <?php endif; ?>

    <div class="layout-row filters-right">
        <aside class="filters">
            <form method="get" action="index.php">
                <input type="hidden" name="route" value="manga/list">
                <div class="form-group">
                    <label>Пошук</label>
                    <input type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Рік від</label>
                    <input type="number" name="yearFrom" min="1900" max="2100" value="<?= htmlspecialchars($filters['yearFrom'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Рік до</label>
                    <input type="number" name="yearTo" min="1900" max="2100" value="<?= htmlspecialchars($filters['yearTo'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select name="status">
                        <option value="">Всі статуси</option>
                        <option value="Ongoing" <?= (isset($filters['status']) && $filters['status']=='Ongoing')?'selected':'' ?>>Ongoing</option>
                        <option value="Completed" <?= (isset($filters['status']) && $filters['status']=='Completed')?'selected':'' ?>>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Тип</label>
                    <select name="type">
                        <option value="">Всі типи</option>
                        <option value="Manga" <?= (isset($filters['type']) && $filters['type']=='Manga')?'selected':'' ?>>Manga</option>
                        <option value="Manhwa" <?= (isset($filters['type']) && $filters['type']=='Manhwa')?'selected':'' ?>>Manhwa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Сортувати за</label>
                    <select name="sort">
                        <option value="title" <?= (isset($filters['sort']) && $filters['sort']=='title')?'selected':'' ?>>Заголовок</option>
                        <option value="year" <?= (isset($filters['sort']) && $filters['sort']=='year')?'selected':'' ?>>Рік</option>
                        <option value="views" <?= (isset($filters['sort']) && $filters['sort']=='views')?'selected':'' ?>>Перегляди</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn">Застосувати фільтри</button>
                    <a href="index.php?route=manga/list" class="btn btn--secondary">Скинути</a>
                </div>
            </form>
        </aside>

        <section class="content">
            <div class="card-grid">
        <?php foreach ($manga as $m): ?>
            <div class="card" style="position:relative">
                <?php $mcover = !empty($m['cover_url']) ? htmlspecialchars($m['cover_url']) : 'https://via.placeholder.com/420x300?text=No+Cover'; ?>
                <a href="index.php?route=manga/view&id=<?= $m['id'] ?>"><img src="<?= $mcover ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="card__img" onerror="this.onerror=null;this.src='https://via.placeholder.com/420x300?text=No+Cover'" /></a>
                <div style="padding-top:6px">
                    <h3 class="card__title"><?= htmlspecialchars($m['title_ua'] ?: $m['title']) ?></h3>
                    <p class="card__text"><?= htmlspecialchars(mb_substr($m['description'] ?? '',0,120)) ?></p>
                    <!-- details by clicking cover/title -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
