<?php
$colors = $colors ?? [];
$currentColor = $currentColor ?? '#f9fafb';
$message = $message ?? '';
?>

<h1>Колір фону (Сесії)</h1>

<p>Оберіть колір фону сторінки. Значення зберігається в <code>$_SESSION</code> та діє на всіх сторінках до закриття браузера.</p>

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

<div class="info-block">
    <h3>Як це працює?</h3>
    <ul>
        <li>Обраний колір зберігається: <code>$_SESSION['bg_color'] = '<?= htmlspecialchars($currentColor) ?>'</code></li>
        <li>В <code>header.php</code> колір застосовується до <code>&lt;body&gt;</code></li>
        <li>Сесія живе до закриття браузера</li>
    </ul>
</div>
