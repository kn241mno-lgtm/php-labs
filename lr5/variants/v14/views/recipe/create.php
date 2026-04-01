<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Додати рецепт</h1>

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
        <label for="r_title" class="form__label">Назва рецепту <span class="required">*</span></label>
        <input type="text" id="r_title" name="title" class="form__input"
               value="<?= htmlspecialchars($old['title'] ?? '') ?>"
               placeholder="Борщ класичний">
        <?php if (isset($errors['title'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['title']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="r_category" class="form__label">Категорія</label>
            <input type="text" id="r_category" name="category" class="form__input"
                   value="<?= htmlspecialchars($old['category'] ?? '') ?>"
                   placeholder="Перші страви, Десерти...">
        </div>

        <div class="form__group <?= isset($errors['cooking_time']) ? 'form__group--error' : '' ?>">
            <label for="r_time" class="form__label">Час приготування (хв)</label>
            <input type="number" id="r_time" name="cooking_time" class="form__input" min="0"
                   value="<?= htmlspecialchars($old['cooking_time'] ?? '') ?>"
                   placeholder="60">
        </div>
    </div>

    <div class="form__group <?= isset($errors['servings']) ? 'form__group--error' : '' ?>">
        <label for="r_servings" class="form__label">Кількість порцій</label>
        <input type="number" id="r_servings" name="servings" class="form__input" min="1"
               value="<?= htmlspecialchars($old['servings'] ?? '') ?>"
               placeholder="4">
    </div>

    <div class="form__group">
        <label for="r_ingredients" class="form__label">Інгредієнти</label>
        <textarea id="r_ingredients" name="ingredients" class="form__textarea"
                  placeholder="Буряк — 2 шт, Капуста — 300г..."><?= htmlspecialchars($old['ingredients'] ?? '') ?></textarea>
    </div>

    <div class="form__group">
        <label for="r_instructions" class="form__label">Інструкція приготування</label>
        <textarea id="r_instructions" name="instructions" class="form__textarea"
                  placeholder="Крок 1: Зварити бульйон..."><?= htmlspecialchars($old['instructions'] ?? '') ?></textarea>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Додати</button>
        <a href="index.php?route=recipe/list" class="btn btn--secondary">Скасувати</a>
    </div>
</form>
