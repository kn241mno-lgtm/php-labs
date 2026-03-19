<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Додати товар</h1>

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

<form method="POST" action="index.php?route=catalog/create" class="form">
    <div class="form__group <?= isset($errors['name']) ? 'form__group--error' : '' ?>">
        <label for="prod_name" class="form__label">Назва <span class="required">*</span></label>
        <input type="text" id="prod_name" name="name" class="form__input"
               value="<?= htmlspecialchars($old['name'] ?? '') ?>"
               placeholder="Назва товару">
        <?php if (isset($errors['name'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group <?= isset($errors['price']) ? 'form__group--error' : '' ?>">
            <label for="prod_price" class="form__label">Ціна (грн) <span class="required">*</span></label>
            <input type="number" id="prod_price" name="price" class="form__input" step="0.01" min="0"
                   value="<?= htmlspecialchars($old['price'] ?? '') ?>"
                   placeholder="0.00">
            <?php if (isset($errors['price'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['price']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form__group">
            <label for="prod_category" class="form__label">Категорія</label>
            <input type="text" id="prod_category" name="category" class="form__input"
                   value="<?= htmlspecialchars($old['category'] ?? '') ?>"
                   placeholder="Електроніка, Аксесуари...">
        </div>
    </div>

    <div class="form__group">
        <label for="prod_desc" class="form__label">Опис</label>
        <textarea id="prod_desc" name="description" class="form__textarea"
                  placeholder="Опис товару..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Додати</button>
        <a href="index.php?route=catalog/list" class="btn btn--secondary">Скасувати</a>
    </div>
</form>
