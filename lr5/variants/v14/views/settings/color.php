<?php
$colors = $colors ?? [];
$currentColor = $currentColor ?? '#f9fafb';
$message = $message ?? '';
$error = $error ?? '';
?>

<h1>Колір фону</h1>

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
