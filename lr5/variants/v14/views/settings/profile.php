<?php
$user = $user ?? [];
$errors = $errors ?? [];
?>

<div class="page">
    <h1>⚙️ Редагування профілю</h1>
    <p style="color: var(--muted); margin-bottom: 24px;">Оновіть інформацію про ваш профіль.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error" style="margin-bottom: 24px;">
            <strong>Виправте помилки:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?route=settings/profile" class="form" style="max-width: 800px;">
        <!-- Основна інформація -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">ℹ️ Основна інформація</h3>
            </div>

            <div class="form__group">
                <label for="edit_login" class="form__label">Логін</label>
                <input type="text" id="edit_login" class="form__input" value="<?= htmlspecialchars($user['login'] ?? '') ?>" disabled>
                <span class="form__hint">Логін змінити не можна</span>
            </div>

            <div class="form__group <?= isset($errors['email']) ? 'form__group--error' : '' ?>">
                <label for="edit_email" class="form__label">E-mail <span class="required" style="color: #ef4444;">*</span></label>
                <input type="email" id="edit_email" name="email" class="form__input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="form__error"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Персональні дані -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">👤 Персональні дані</h3>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form__group <?= isset($errors['first_name']) ? 'form__group--error' : '' ?>">
                    <label for="edit_first_name" class="form__label">Ім'я <span class="required" style="color: #ef4444;">*</span></label>
                    <input type="text" id="edit_first_name" name="first_name" class="form__input" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                </div>

                <div class="form__group <?= isset($errors['last_name']) ? 'form__group--error' : '' ?>">
                    <label for="edit_last_name" class="form__label">Прізвище <span class="required" style="color: #ef4444;">*</span></label>
                    <input type="text" id="edit_last_name" name="last_name" class="form__input" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form__group">
                    <label for="edit_phone" class="form__label">📱 Телефон</label>
                    <input type="tel" id="edit_phone" name="phone" class="form__input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>

                <div class="form__group">
                    <label for="edit_city" class="form__label">📍 Місто</label>
                    <input type="text" id="edit_city" name="city" class="form__input" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                </div>
            </div>

            <div class="form__group">
                <label class="form__label">Стать</label>
                <div style="display: flex; gap: 24px; margin-top: 8px;">
                    <label class="form__radio">
                        <input type="radio" name="gender" value="male" <?= ($user['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                        <span>♂️ Чоловіча</span>
                    </label>
                    <label class="form__radio">
                        <input type="radio" name="gender" value="female" <?= ($user['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        <span>♀️ Жіноча</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Додаткові дані -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">📝 Додаткові дані</h3>
            </div>

            <div class="form__group">
                <label for="edit_about" class="form__label">Про себе</label>
                <textarea id="edit_about" name="about" class="form__textarea" placeholder="Розповідь про себе..."><?= htmlspecialchars($user['about'] ?? '') ?></textarea>
                <span class="form__hint">Максимум 500 символів</span>
            </div>

            <div class="form__group">
                <label for="edit_display_name" class="form__label">Відображуване ім'я</label>
                <input type="text" id="edit_display_name" name="display_name" class="form__input" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>" placeholder="Як ви хочете бути названі">
                <span class="form__hint">Якщо не заповнено, буде використано ім'я та прізвище</span>
            </div>

            <div class="form__group">
                <label for="edit_avatar_url" class="form__label">🖼️ URL аватарки</label>
                <input type="url" id="edit_avatar_url" name="avatar_url" class="form__input" value="<?= htmlspecialchars($user['avatar_url'] ?? '') ?>" placeholder="https://example.com/avatar.jpg">
                <span class="form__hint">Посилання на зображення для аватки профілю</span>
            </div>
        </div>

        <!-- Дії -->
        <div class="card">
            <div class="form__actions">
                <button type="submit" class="btn">💾 Зберегти зміни</button>
                <a href="index.php?route=auth/profile" class="btn" style="background: #334155;">← Назад до профілю</a>
            </div>
        </div>
    </form>
</div>
