<?php
$colors = $colors ?? [];
$currentColor = $currentColor ?? '#f9fafb';
$message = $message ?? '';
$error = $error ?? '';
?>

<h1>Колір фону (Сесії)</h1>

<p>Оберіть колір фону сторінки. Значення зберігається в <code>$_SESSION</code> та діє на всіх сторінках до закриття браузера.</p>

<?php if ($error !== ''): ?>
    <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($message !== ''): ?>
    <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" action="index.php?route=settings/color" class="form">
    <div class="color-picker">
        <?php foreach ($colors as $hex => $label): ?>
            <label class="color-picker__item <?= $currentColor === $hex ? 'color-picker__item--active' : '' ?>">
                <input type="radio" name="bg_color" value="<?= htmlspecialchars($hex) ?>"
                    <?= $currentColor === $hex ? 'checked' : '' ?>>
                <span class="color-picker__swatch" style="background-color: <?= htmlspecialchars($hex) ?>"></span>
                <span class="color-picker__label"><?= htmlspecialchars($label) ?></span>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Зберегти колір</button>
    </div>
</form>

<p class="text-muted text-muted--mt">Модуль успадковано з ЛР4. Також доступне <a href="index.php?route=settings/greeting">привітання через Cookie</a>.</p>
