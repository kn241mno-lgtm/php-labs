<?php
$user = $user ?? [];
?>

<h1>Профіль: <?= htmlspecialchars($user['login'] ?? '') ?></h1>

<div class="profile">
    <table class="table">
        <tbody>
            <tr><td><strong>Логін</strong></td><td><?= htmlspecialchars($user['login'] ?? '') ?></td></tr>
            <tr><td><strong>Ім'я</strong></td><td><?= htmlspecialchars($user['first_name'] ?? '') ?></td></tr>
            <tr><td><strong>Прізвище</strong></td><td><?= htmlspecialchars($user['last_name'] ?? '') ?></td></tr>
            <tr><td><strong>E-mail</strong></td><td><?= htmlspecialchars($user['email'] ?? '') ?></td></tr>
            <tr><td><strong>Телефон</strong></td><td><?= htmlspecialchars($user['phone'] ?? '-') ?></td></tr>
            <tr><td><strong>Місто</strong></td><td><?= htmlspecialchars($user['city'] ?? '-') ?></td></tr>
            <tr><td><strong>Стать</strong></td><td><?= ($user['gender'] ?? '') === 'female' ? 'Жіноча' : (($user['gender'] ?? '') === 'male' ? 'Чоловіча' : '-') ?></td></tr>
            <tr><td><strong>Про себе</strong></td><td><?= htmlspecialchars($user['about'] ?? '-') ?></td></tr>
            <tr><td><strong>Дата реєстрації</strong></td><td><?= htmlspecialchars($user['created_at'] ?? '') ?></td></tr>
        </tbody>
    </table>

    <div class="form__actions">
        <a href="index.php?route=auth/edit" class="btn">Редагувати</a>
        <a href="index.php?route=auth/delete" class="btn btn--danger">Видалити акаунт</a>
        <a href="index.php?route=auth/logout" class="btn btn--secondary">Вийти</a>
    </div>
</div>
