<?php
$message = $message ?? '';
$messageType = $messageType ?? 'success';
$currentName = $currentName ?? '';
$currentGender = $currentGender ?? '';
?>

<h1>Привітання (Cookie)</h1>
<p>Введіть ваше ім'я та стать. Привітання зберігається в cookie на 30 днів і відображається на всіх сторінках.</p>

<?php if ($message !== ''): ?>
    <div class="alert alert--<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($currentName !== ''): ?>
    <?php
    $titleText = $currentGender === 'female' ? 'пані' : 'пане';
    ?>
    <div class="alert alert--info">
        Поточне привітання: <strong>Вітаємо Вас, <?= $titleText ?> <?= htmlspecialchars($currentName) ?>!</strong>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?route=settings/greeting" class="form">
    <div class="form__group">
        <label for="greeting_name" class="form__label">Ваше ім'я</label>
        <input type="text" id="greeting_name" name="greeting_name"
               class="form__input"
               value="<?= htmlspecialchars($currentName) ?>"
               placeholder="Введіть ваше ім'я">
    </div>

    <div class="form__group">
        <span class="form__label">Стать</span>
        <div class="form__radio-group">
            <label class="form__radio">
                <input type="radio" name="greeting_gender" value="male"
                       <?= $currentGender === 'male' ? 'checked' : '' ?>>
                Чоловіча
            </label>
            <label class="form__radio">
                <input type="radio" name="greeting_gender" value="female"
                       <?= $currentGender === 'female' ? 'checked' : '' ?>>
                Жіноча
            </label>
        </div>
    </div>

    <button type="submit" class="btn">Зберегти</button>
</form>
