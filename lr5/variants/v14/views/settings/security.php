<div class="page">
    <h1>🔒 Безпека</h1>
    <p style="color: var(--muted); margin-bottom: 24px;">Налаштування безпеки вашого облікового запису.</p>

    <div style="max-width: 800px;">
        <!-- Two-Factor Authentication -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">🔐 Двофакторна аутентифікація</h3>
            </div>
            <div class="security-item">
                <div class="security-item-label">
                    <div class="security-item-title">Статус: Відключена</div>
                    <div class="security-item-desc">Додайте додаткові рівні захисту для вашого облікового запису</div>
                </div>
                <button class="btn btn--small">Увімкнути</button>
            </div>
        </div>

        <!-- Profile Visibility -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">👁️ Видимість профілю</h3>
            </div>
            <div class="security-item">
                <div class="security-item-label">
                    <div class="security-item-title">Дозволити іншим переглядати профіль</div>
                    <div class="security-item-desc">Інші користувачі зможуть бачити вашу аватарку, ім'я та список улюблених</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Public Lists -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">📋 Публічні списки</h3>
            </div>
            <div class="security-item">
                <div class="security-item-label">
                    <div class="security-item-title">Залишати списки аніме/манги публічними</div>
                    <div class="security-item-desc">Дозволити іншим бачити ваші списки та статуси перегляду</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">🔑 Змінити пароль</h3>
            </div>
            <form method="post" action="index.php?route=settings/security" class="form">
                <div class="form__group">
                    <label for="current_password" class="form__label">Поточний пароль <span class="required">*</span></label>
                    <input type="password" id="current_password" name="current_password" class="form__input" required>
                </div>

                <div class="form__group">
                    <label for="new_password" class="form__label">Новий пароль <span class="required">*</span></label>
                    <input type="password" id="new_password" name="new_password" class="form__input" required>
                    <span class="form__hint">Мінімум 8 символів</span>
                </div>

                <div class="form__group">
                    <label for="confirm_password" class="form__label">Підтвердіть пароль <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form__input" required>
                </div>

                <div class="form__actions">
                    <button type="submit" class="btn">🔐 Змінити пароль</button>
                </div>
            </form>
        </div>

        <!-- Active Sessions -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="profile-section-header">
                <h3 style="margin: 0;">💻 Активні сеанси</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="padding: 12px; background: #071028; border-radius: 6px; border-left: 3px solid #2563eb;">
                    <div style="font-weight: 600; color: #e6eef8;">Chrome на Windows</div>
                    <div style="font-size: 0.85rem; color: var(--muted); margin-top: 4px;">Остання активність: прямо зараз</div>
                </div>
                <p style="color: var(--muted); font-size: 0.9rem; margin: 0;">У вас 1 активний сеанс</p>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card" style="padding: 20px; background: rgba(239, 68, 68, 0.05); border: 2px solid rgba(239, 68, 68, 0.2); border-radius: 10px;">
            <h3 style="margin: 0 0 12px 0; color: #ef4444;">⚠️ Небезпечна зона</h3>
            <p style="color: var(--muted); font-size: 0.9rem; margin: 0 0 16px 0;">Видалення облікового запису не можна повернути. Всі ваші дані будуть видалені.</p>
            <button class="btn btn--danger">🗑️ Видалити облік навсіки</button>
        </div>
    </div>
</div>
