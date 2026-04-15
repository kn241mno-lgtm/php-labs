<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Додати статтю / новину</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <strong>Помилки:</strong>
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?route=recipe/create" class="form">
    <div class="form__group <?= isset($errors['title']) ? 'form__group--error' : '' ?>">
        <label for="a_title" class="form__label">Заголовок <span class="required">*</span></label>
        <input type="text" id="a_title" name="title" class="form__input"
               value="<?= htmlspecialchars($old['title'] ?? '') ?>"
               placeholder="Анонс нового сезону...">
        <?php if (isset($errors['title'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['title']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group">
        <label for="a_summary" class="form__label">Короткий опис / анонс</label>
        <textarea id="a_summary" name="summary" class="form__textarea"><?= htmlspecialchars($old['summary'] ?? '') ?></textarea>
    </div>

    <div class="form__group">
        <label for="a_content" class="form__label">Повний контент</label>
        <textarea id="a_content" name="content" class="form__textarea"><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="a_category" class="form__label">Категорія</label>
            <input type="text" id="a_category" name="category" class="form__input"
                   value="<?= htmlspecialchars($old['category'] ?? '') ?>"
                   placeholder="Новини, Огляди, Анонси...">
        </div>
        <div class="form__group">
            <label for="a_image" class="form__label">URL зображення</label>
            <input type="text" id="a_image" name="image_url" class="form__input" value="<?= htmlspecialchars($old['image_url'] ?? '') ?>">
        </div>
    </div>

    <div class="form__group">
        <label class="form__label"><input type="checkbox" name="is_published" <?= isset($old['is_published']) ? 'checked' : '' ?>> Опублікувати одразу</label>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Додати статтю</button>
        <a href="index.php?route=recipe/list" class="btn btn--secondary">Скасувати</a>
    </div>
</form>
