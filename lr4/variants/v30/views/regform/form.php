<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Реєстрація на кулінарному блозі</h1>
<p>Створіть акаунт, щоб зберігати рецепти та залишати коментарі.</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <strong>Помилки при заповненні форми:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?route=regform/form" class="form">
    <div class="form__group">
        <label for="login" class="form__label">Логін (нікнейм)</label>
        <input type="text" id="login" name="login"
               class="form__input<?= isset($errors['login']) ? ' form__input--error' : '' ?>"
               value="<?= htmlspecialchars($old['login'] ?? '') ?>"
               placeholder="Ваш нікнейм (без пробілів, без цифр, мін. 5 символів)">
        <?php if (isset($errors['login'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['login']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="password" class="form__label">Пароль</label>
            <input type="password" id="password" name="password"
                   class="form__input<?= isset($errors['password']) ? ' form__input--error' : '' ?>"
                   placeholder="Мін. 5 символів, має містити цифру">
            <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form__group">
            <label for="password_confirm" class="form__label">Підтвердження паролю</label>
            <input type="password" id="password_confirm" name="password_confirm"
                   class="form__input<?= isset($errors['password_confirm']) ? ' form__input--error' : '' ?>"
                   placeholder="Повторіть пароль">
            <?php if (isset($errors['password_confirm'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password_confirm']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form__group">
        <label for="about" class="form__label">Про себе (необов'язково)</label>
        <textarea id="about" name="about" class="form__textarea" rows="4"
                  placeholder="Ваш кулінарний досвід, улюблені страви, рівень майстерності..."><?= htmlspecialchars($old['about'] ?? '') ?></textarea>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зареєструватися</button>
        <button type="reset" class="btn btn--secondary">Очистити</button>
    </div>
</form>
