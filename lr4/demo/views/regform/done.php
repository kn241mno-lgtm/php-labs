<?php $regData = $regData ?? []; ?>

<div class="success-page">
    <h1>Реєстрація успішна!</h1>
    <div class="alert alert--success">
        <p>Дякуємо за реєстрацію<?php if (!empty($regData['first_name'])): ?>,
            <strong><?= htmlspecialchars($regData['first_name']) ?> <?= htmlspecialchars($regData['last_name'] ?? '') ?></strong><?php endif; ?>!</p>
        <?php if (!empty($regData['email'])): ?>
            <p>Лист підтвердження надіслано на <strong><?= htmlspecialchars($regData['email']) ?></strong>.</p>
        <?php endif; ?>
    </div>
    <a href="index.php?route=regform/form" class="btn">Нова реєстрація</a>
    <a href="index.php" class="btn btn--secondary">На головну</a>
</div>
