<?php
$recipe = $recipe ?? [];
$errors = $errors ?? [];
?>

<h1>Редагувати статтю #<?= (int)($recipe['id'] ?? 0) ?></h1>

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

<form method="POST" action="index.php?route=recipe/edit&id=<?= (int)($recipe['id'] ?? 0) ?>" class="form">
    <div class="form__group <?= isset($errors['title']) ? 'form__group--error' : '' ?>">
        <label for="a_title" class="form__label">Заголовок <span class="required">*</span></label>
         <input type="text" id="a_title" name="title" class="form__input"
             value="<?= htmlspecialchars($recipe['title'] ?? '') ?>">
        <?php if (isset($errors['title'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['title']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group">
        <label for="a_summary" class="form__label">Короткий опис / анонс</label>
        <textarea id="a_summary" name="summary" class="form__textarea"><?= htmlspecialchars($recipe['summary'] ?? '') ?></textarea>
    </div>

    <div class="form__group">
        <label for="a_content" class="form__label">Повний контент</label>
        <textarea id="a_content" name="content" class="form__textarea"><?= htmlspecialchars($recipe['content'] ?? '') ?></textarea>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="a_category" class="form__label">Категорія</label>
                 <input type="text" id="a_category" name="category" class="form__input"
                     value="<?= htmlspecialchars($recipe['category'] ?? '') ?>">
        </div>
        <div class="form__group">
            <label for="a_image" class="form__label">URL зображення</label>
            <input type="text" id="a_image" name="image_url" class="form__input" value="<?= htmlspecialchars($recipe['image_url'] ?? '') ?>">
        </div>
    </div>

    <div class="form__group">
        <label class="form__label"><input type="checkbox" name="is_published" <?= (isset($recipe['is_published']) && $recipe['is_published']) ? 'checked' : '' ?>> Опубліковано</label>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зберегти</button>
        <a href="index.php?route=recipe/list" class="btn btn--secondary">Скасувати</a>
    </div>
</form>
