<?php
$message = $message ?? '';
$currentName = $currentName ?? '';
$currentGender = $currentGender ?? '';
?>

<h1>Привітання (Cookie)</h1>

<p>Введіть ваше ім'я та стать. Дані зберігаються в <code>cookie</code> (30 днів) та відображаються у шапці на всіх сторінках.</p>

<?php if ($message !== ''): ?>
    <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($currentName !== ''): ?>
    <div class="alert alert--info">
        Поточне привітання:
        <strong>&laquo;Вітаємо Вас, <?= $currentGender === 'female' ? 'пані' : 'пане' ?> <?= htmlspecialchars($currentName) ?>!&raquo;</strong>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?route=settings/greeting" class="form">
    <div class="form__group">
        <label for="greeting_name" class="form__label">Ваше ім'я <span class="required">*</span></label>
        <input type="text" id="greeting_name" name="greeting_name" class="form__input"
               value="<?= htmlspecialchars($currentName) ?>"
               placeholder="Введіть ваше ім'я">
    </div>

    <div class="form__group">
        <span class="form__label">Стать <span class="required">*</span></span>
        <div class="form__radio-group">
            <label class="form__radio">
                <input type="radio" name="greeting_gender" value="male"
                    <?= $currentGender === 'male' || $currentGender === '' ? 'checked' : '' ?>>
                <span>Чоловіча (пане)</span>
            </label>
            <label class="form__radio">
                <input type="radio" name="greeting_gender" value="female"
                    <?= $currentGender === 'female' ? 'checked' : '' ?>>
                <span>Жіноча (пані)</span>
            </label>
        </div>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зберегти в Cookie</button>
    </div>
</form>

<div class="info-block">
    <h3>Як це працює?</h3>
    <ul>
        <li><code>setcookie('greeting_name', '...', time() + 30*24*3600)</code> — зберігає ім'я на 30 днів</li>
        <li><code>setcookie('greeting_gender', '...', time() + 30*24*3600)</code> — зберігає стать</li>
        <li>В <code>header.php</code> зчитується <code>$_COOKIE['greeting_name']</code> та формується привітання</li>
        <li>Cookie зберігається в браузері та надсилається з кожним запитом</li>
    </ul>
</div>
