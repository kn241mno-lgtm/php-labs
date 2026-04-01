<?php
$comments = $comments ?? [];
$message = $message ?? '';
$errors = $errors ?? [];
?>

<h1>Гостьова книга</h1>
<p>Коментарі зберігаються у файлі <code>data/comments.txt</code> (формат: <code>дата|ім'я|коментар</code>).</p>

<?php if ($message !== ''): ?>
    <div class="alert alert--success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h2>Додати коментар</h2>
<form method="POST" action="index.php?route=guestbook/index" class="form">
    <div class="form__group <?= isset($errors['name']) ? 'form__group--error' : '' ?>">
        <label for="gb_name" class="form__label">Ім'я <span class="required">*</span></label>
        <input type="text" id="gb_name" name="name" class="form__input"
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
               placeholder="Ваше ім'я">
        <?php if (isset($errors['name'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['name']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__group <?= isset($errors['comment']) ? 'form__group--error' : '' ?>">
        <label for="gb_comment" class="form__label">Коментар <span class="required">*</span></label>
        <textarea id="gb_comment" name="comment" class="form__textarea"
                  placeholder="Ваш коментар..."><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
        <?php if (isset($errors['comment'])): ?>
            <span class="form__error"><?= htmlspecialchars($errors['comment']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form__actions">
        <button type="submit" class="btn">Додати</button>
    </div>
</form>

<h2>Коментарі (<?= count($comments) ?>)</h2>

<?php if (empty($comments)): ?>
    <p class="text-muted">Поки що коментарів немає.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Дата</th>
                <th>Ім'я</th>
                <th>Коментар</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['date']) ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['comment']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
