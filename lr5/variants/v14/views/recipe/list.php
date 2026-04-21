<?php
$recipes = $recipes ?? [];
?>

<h1>Новини та статті</h1>

<div class="form__actions" style="margin-bottom: 20px">
    <a href="index.php?route=recipe/create" class="btn">Додати статтю</a>
    <a href="index.php?route=anime/list" class="btn btn--secondary">Перейти до каталогу аніме</a>
    <a href="index.php?route=manga/list" class="btn btn--secondary">Перейти до каталогу манги</a>
</div>

<?php if (empty($recipes)): ?>
    <p class="text-muted">Статей ще немає.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Категорія</th>
                <th>Опубл.</th>
                <th>Автор (ID)</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $r): ?>
                <tr>
                    <td><?= (int)($r['id'] ?? 0) ?></td>
                    <td><?= htmlspecialchars($r['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['category'] ?? '') ?></td>
                    <td><?= htmlspecialchars(isset($r['is_published']) && $r['is_published'] ? 'Так' : 'Ні') ?></td>
                    <td><?= (int)($r['author_id'] ?? 0) ?></td>
                    <td class="table__actions">
                        <a href="index.php?route=recipe/edit&id=<?= (int)($r['id'] ?? 0) ?>" class="btn btn--small">Редагувати</a>
                        <form method="POST" action="index.php?route=recipe/delete" style="display:inline"
                              onsubmit="return confirm('Видалити статтю?')">
                            <input type="hidden" name="id" value="<?= (int)($r['id'] ?? 0) ?>">
                            <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
