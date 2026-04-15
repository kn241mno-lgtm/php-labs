<div class="page">
    <h1>Каталог манги</h1>

    <div class="card-grid">
        <?php foreach ($manga as $m): ?>
            <div class="card">
                <h3 class="card__title"><?= htmlspecialchars($m['title']) ?></h3>
                <p class="card__text"><?= htmlspecialchars($m['description'] ? (mb_substr($m['description'],0,160).'...') : '') ?></p>
                <a href="index.php?route=manga/view&id=<?= $m['id'] ?>" class="btn btn--small">Деталі</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
