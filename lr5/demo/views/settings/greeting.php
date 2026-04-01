<?php
$message = $message ?? '';
$error = $error ?? '';
$currentName = $currentName ?? '';
$currentGender = $currentGender ?? '';
?>

<h1>Привітання (Cookie)</h1>

<p>Введіть ваше ім'я та стать. Дані зберігаються в <code>cookie</code> (30 днів) та відображаються у шапці на всіх сторінках.</p>

<?php if ($error !== ''): ?>
    <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

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

    <fieldset class="form__group form__fieldset">
        <legend class="form__label">Стать <span class="required">*</span></legend>
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
    </fieldset>

    <div class="form__actions">
        <button type="submit" class="btn">Зберегти в Cookie</button>
    </div>
</form>

<p class="text-muted text-muted--mt">Модуль успадковано з ЛР4. Також доступна <a href="index.php?route=settings/color">зміна кольору фону</a>.</p>
