<div class="page">
    <h1>Кастомізація</h1>
    <p style="color: var(--muted); margin-bottom: 24px;">Налаштування зовнішнього вигляду інтерфейсу.</p>

    <div style="max-width: 600px;">
        <form method="post" action="#" class="form">
            <div class="form__group">
                <label class="form__label">🎨 Виберіть тему</label>
                <div class="theme-options" style="grid-template-columns: repeat(2, 1fr);">
                    <label class="theme-option">
                        <input type="radio" name="theme" value="dark" checked>
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #0a1420, #061022);"></div>
                        <span class="theme-option-label">Темна</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="light">
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #f3f4f6, #e5e7eb);"></div>
                        <span class="theme-option-label">Світла</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="blue">
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #1e3a8a, #1e40af);"></div>
                        <span class="theme-option-label">Синя</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="purple">
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #4c1d95, #6b21a8);"></div>
                        <span class="theme-option-label">Фіолетова</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="green">
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #064e3b, #065f46);"></div>
                        <span class="theme-option-label">Зелена</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="orange">
                        <div class="theme-option-preview" style="background: linear-gradient(135deg, #7c2d12, #92400e);"></div>
                        <span class="theme-option-label">Помаранчева</span>
                    </label>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label">📐 Розмір шрифту</label>
                <div style="display: flex; gap: 12px;">
                    <label class="form__radio">
                        <input type="radio" name="font_size" value="small">
                        <span>Малий</span>
                    </label>
                    <label class="form__radio">
                        <input type="radio" name="font_size" value="normal" checked>
                        <span>Звичайний</span>
                    </label>
                    <label class="form__radio">
                        <input type="radio" name="font_size" value="large">
                        <span>Великий</span>
                    </label>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label">⚡ Анімації</label>
                <label class="toggle-label" style="display: flex; align-items: center; gap: 12px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="animations" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="color: var(--muted);">Включити анімації переходів</span>
                </label>
            </div>

            <div class="form__actions">
                <button class="btn" type="submit">💾 Зберегти налаштування</button>
            </div>
        </form>
    </div>
</div>
