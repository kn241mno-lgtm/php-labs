<div class="page">
    <h1>Редагувати аніме</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Заголовок</label>
            <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
        </div>
        <div class="form-group">
            <label>Рік</label>
            <input type="number" name="year" value="<?= htmlspecialchars($item['year']) ?>">
        </div>
        <div class="form-group">
            <label>Тип</label>
            <input type="text" name="type" value="<?= htmlspecialchars($item['type']) ?>">
        </div>
        <div class="form-group">
            <label>Статус</label>
            <input type="text" name="status" value="<?= htmlspecialchars($item['status']) ?>">
        </div>
        <div class="form-group">
            <label>Епізоди</label>
            <input type="number" name="episodes" value="<?= htmlspecialchars($item['episodes']) ?>">
        </div>
        <div class="form-group">
            <label>Опис</label>
            <textarea name="description" rows="6"><?= htmlspecialchars($item['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Cover URL</label>
            <input type="text" name="cover_url" value="<?= htmlspecialchars($item['cover_url']) ?>">
        </div>

        <button class="btn btn-primary">Оновити</button>
    </form>
</div>
