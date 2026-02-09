<?php
$old = $old ?? [];
$errors = $errors ?? [];

$cities = ['Kyiv' => 'Київ', 'Lviv' => 'Львів', 'Odesa' => 'Одеса', 'Kharkiv' => 'Харків', 'Dnipro' => 'Дніпро'];
$hobbies = ['programming' => 'Програмування', 'music' => 'Музика', 'sports' => 'Спорт', 'reading' => 'Читання'];
$selectedHobbies = $old['hobbies'] ?? [];
?>

<h1>Форма реєстрації</h1>

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

<form method="POST" action="index.php?route=regform/form" class="form" novalidate>

    <div class="form__group <?= isset($errors['first_name']) ? 'form__group--error' : '' ?>">
        <label for="first_name" class="form__label">Ім'я (англійською) <span class="required">*</span></label>
        <input type="text" id="first_name" name="first_name" class="form__input"
               value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
               placeholder="John">
        <?php if (isset($errors['first_name'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['first_name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group <?= isset($errors['last_name']) ? 'form__group--error' : '' ?>">
        <label for="last_name" class="form__label">Прізвище (англійською) <span class="required">*</span></label>
        <input type="text" id="last_name" name="last_name" class="form__input"
               value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
               placeholder="Doe">
        <?php if (isset($errors['last_name'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['last_name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group <?= isset($errors['email']) ? 'form__group--error' : '' ?>">
        <label for="email" class="form__label">E-mail <span class="required">*</span></label>
        <input type="email" id="email" name="email" class="form__input"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
               placeholder="john@example.com">
        <?php if (isset($errors['email'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['email']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__row">
        <div class="form__group <?= isset($errors['password']) ? 'form__group--error' : '' ?>">
            <label for="password" class="form__label">Пароль <span class="required">*</span></label>
            <input type="password" id="password" name="password" class="form__input"
                   placeholder="Мінімум 6 символів">
            <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form__group <?= isset($errors['password_confirm']) ? 'form__group--error' : '' ?>">
            <label for="password_confirm" class="form__label">Підтвердження пароля <span class="required">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm" class="form__input"
                   placeholder="Повторіть пароль">
            <?php if (isset($errors['password_confirm'])): ?>
                <span class="form__error"><?= htmlspecialchars($errors['password_confirm']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form__group <?= isset($errors['gender']) ? 'form__group--error' : '' ?>">
        <span class="form__label">Стать <span class="required">*</span></span>
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
        <?php if (isset($errors['gender'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['gender']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group <?= isset($errors['city']) ? 'form__group--error' : '' ?>">
        <label for="city" class="form__label">Місто <span class="required">*</span></label>
        <select id="city" name="city" class="form__select">
            <option value="">-- Оберіть місто --</option>
            <?php foreach ($cities as $value => $label): ?>
                <option value="<?= $value ?>" <?= ($old['city'] ?? '') === $value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['city'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['city']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group">
        <span class="form__label">Хобі</span>
        <div class="form__checkbox-group">
            <?php foreach ($hobbies as $value => $label): ?>
                <label class="form__checkbox">
                    <input type="checkbox" name="hobbies[]" value="<?= $value ?>"
                        <?= in_array($value, $selectedHobbies) ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($label) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form__group <?= isset($errors['about']) ? 'form__group--error' : '' ?>">
        <label for="about" class="form__label">Про себе</label>
        <textarea id="about" name="about" class="form__textarea" rows="4"
                  placeholder="Розкажіть про себе (необов'язково, макс. 500 символів)"><?= htmlspecialchars($old['about'] ?? '') ?></textarea>
        <?php if (isset($errors['about'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['about']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зареєструватися</button>
        <button type="reset" class="btn btn--secondary">Очистити</button>
    </div>
</form>
