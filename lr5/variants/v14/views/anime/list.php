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
