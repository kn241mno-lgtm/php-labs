<div class="page">
    <h1>Каталог аніме</h1>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?route=anime/create" class="btn btn-primary">Додати аніме</a>
    <?php endif; ?>

    <div class="card-grid">
        <?php foreach ($anime as $a): ?>
            <div class="card">
                <h3 class="card__title"><?= htmlspecialchars($a['title']) ?></h3>
                <p class="card__text"><?= htmlspecialchars($a['description'] ? (mb_substr($a['description'],0,160).'...') : '') ?></p>
                <a href="index.php?route=anime/view&id=<?= $a['id'] ?>" class="btn btn--small">Деталі</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
