<div class="page">
    <h1>🔔 Сповіщення</h1>
    <p style="color: var(--muted); margin-bottom: 24px;">Налаштування отримання сповіщень та інформування.</p>

    <div style="max-width: 800px;">
        <form method="post" action="index.php?route=settings/notifications" class="form">
            <!-- Site Notifications -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="profile-section-header">
                    <h3 style="margin: 0;">📱 Сповіщення з сайту</h3>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="notif-new-release" name="notif_new_release" checked>
                    <label for="notif-new-release" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Новий випуск аніме</span>
                        <span class="notification-item-desc">Отримуйте сповіщення про нові випуски ваших улюблених аніме</span>
                    </label>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="notif-comments" name="notif_comments" checked>
                    <label for="notif-comments" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Коментарі</span>
                        <span class="notification-item-desc">Отримуйте сповіщення на коментарі до ваших постів</span>
                    </label>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="notif-recommendations" name="notif_recommendations" checked>
                    <label for="notif-recommendations" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Рекомендації</span>
                        <span class="notification-item-desc">Отримуйте рекомендації на основі ваших переглядів</span>
                    </label>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="notif-system" name="notif_system" checked>
                    <label for="notif-system" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Системні сповіщення</span>
                        <span class="notification-item-desc">Важливі оновлення та повідомлення про сайт</span>
                    </label>
                </div>
            </div>

            <!-- Email Notifications -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="profile-section-header">
                    <h3 style="margin: 0;">✉️ Email сповіщення</h3>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="email-weekly" name="email_weekly" checked>
                    <label for="email-weekly" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Тижневий зведення</span>
                        <span class="notification-item-desc">Отримуйте тижневе резюме нових випусків та цікавого контенту</span>
                    </label>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="email-important" name="email_important">
                    <label for="email-important" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Тільки важливе</span>
                        <span class="notification-item-desc">Тільки критичні повідомлення та сповіщення про безпеку</span>
                    </label>
                </div>

                <div class="notification-item">
                    <input type="checkbox" id="email-marketing" name="email_marketing">
                    <label for="email-marketing" style="display: flex; flex-direction: column; gap: 4px;">
                        <span class="notification-item-title">Маркетингові листи</span>
                        <span class="notification-item-desc">Новинки, спеціальні пропозиції та анонси нових функцій</span>
                    </label>
                </div>
            </div>

            <!-- Email Frequency -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="profile-section-header">
                    <h3 style="margin: 0;">⏱️ Частота повідомлень</h3>
                </div>

                <div class="form__group">
                    <label class="form__label">Як часто ви хотите отримувати email?</label>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                        <label class="form__radio">
                            <input type="radio" name="email_frequency" value="immediate" checked>
                            <span>Негайно</span>
                        </label>
                        <label class="form__radio">
                            <input type="radio" name="email_frequency" value="daily">
                            <span>Щодня</span>
                        </label>
                        <label class="form__radio">
                            <input type="radio" name="email_frequency" value="weekly">
                            <span>Щотижня</span>
                        </label>
                        <label class="form__radio">
                            <input type="radio" name="email_frequency" value="never">
                            <span>Ніколи</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="form__actions">
                    <button type="submit" class="btn">💾 Зберегти налаштування</button>
                    <a href="index.php?route=settings/profile" class="btn" style="background: #334155;">← Назад</a>
                </div>
            </div>
        </form>
    </div>
</div>
