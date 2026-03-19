<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Реєстрація</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <strong>Виправте помилки:</strong>
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?route=auth/register" class="form">
    <div class="form__group <?= isset($errors['login']) ? 'form__group--error' : '' ?>">
        <label for="reg_login" class="form__label">Логін <span class="required">*</span></label>
        <input type="text" id="reg_login" name="login" class="form__input"
               value="<?= htmlspecialchars($old['login'] ?? '') ?>"
               placeholder="3-30 символів (латинські, цифри, _)">
        <?php if (isset($errors['login'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['login']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group <?= isset($errors['password']) ? 'form__group--error' : '' ?>">
            <label for="reg_password" class="form__label">Пароль <span class="required">*</span></label>
            <input type="password" id="reg_password" name="password" class="form__input"
                   placeholder="Мінімум 6 символів">
            <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form__group <?= isset($errors['password_confirm']) ? 'form__group--error' : '' ?>">
            <label for="reg_password_confirm" class="form__label">Підтвердження <span class="required">*</span></label>
            <input type="password" id="reg_password_confirm" name="password_confirm" class="form__input"
                   placeholder="Повторіть пароль">
            <?php if (isset($errors['password_confirm'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password_confirm']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form__group <?= isset($errors['email']) ? 'form__group--error' : '' ?>">
        <label for="reg_email" class="form__label">E-mail <span class="required">*</span></label>
        <input type="email" id="reg_email" name="email" class="form__input"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
               placeholder="user@example.com">
        <?php if (isset($errors['email'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['email']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group <?= isset($errors['first_name']) ? 'form__group--error' : '' ?>">
            <label for="reg_first_name" class="form__label">Ім'я <span class="required">*</span></label>
            <input type="text" id="reg_first_name" name="first_name" class="form__input"
                   value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
            <?php if (isset($errors['first_name'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['first_name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form__group <?= isset($errors['last_name']) ? 'form__group--error' : '' ?>">
            <label for="reg_last_name" class="form__label">Прізвище <span class="required">*</span></label>
            <input type="text" id="reg_last_name" name="last_name" class="form__input"
                   value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
            <?php if (isset($errors['last_name'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['last_name']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form__row">
        <div class="form__group">
            <label for="reg_phone" class="form__label">Телефон</label>
            <input type="tel" id="reg_phone" name="phone" class="form__input"
                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                   placeholder="+380...">
        </div>

        <div class="form__group">
            <label for="reg_city" class="form__label">Місто</label>
            <input type="text" id="reg_city" name="city" class="form__input"
                   value="<?= htmlspecialchars($old['city'] ?? '') ?>">
        </div>
    </div>

    <div class="form__group">
        <span class="form__label">Стать</span>
        <div class="form__radio-group">
            <label class="form__radio">
                <input type="radio" name="gender" value="male"
                    <?= ($old['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                <span>Чоловіча</span>
            </label>
            <label class="form__radio">
                <input type="radio" name="gender" value="female"
                    <?= ($old['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                <span>Жіноча</span>
            </label>
        </div>
    </div>

    <div class="form__group">
        <label for="reg_about" class="form__label">Про себе</label>
        <textarea id="reg_about" name="about" class="form__textarea"
                  placeholder="Коротко про себе..."><?= htmlspecialchars($old['about'] ?? '') ?></textarea>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зареєструватися</button>
        <a href="index.php?route=auth/login" class="btn btn--secondary">Вже є акаунт? Увійти</a>
    </div>
</form>
