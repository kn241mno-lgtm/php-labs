<?php
$recipes = $recipes ?? [];
?>

<h1>Рецепти</h1>
<p>Колекція рецептів кулінарного блогу. CRUD через PDO (prepared statements).</p>

<div class="form__actions" style="margin-bottom: 20px">
    <a href="index.php?route=recipe/create" class="btn">Додати рецепт</a>
</div>

<?php if (empty($recipes)): ?>
    <p class="text-muted">Рецептів ще немає.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Категорія</th>
                <th>Час (хв)</th>
                <th>Порції</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['category']) ?></td>
                    <td><?= (int)$r['cooking_time'] ?></td>
                    <td><?= (int)$r['servings'] ?></td>
                    <td class="table__actions">
                        <a href="index.php?route=recipe/edit&id=<?= (int)$r['id'] ?>" class="btn btn--small">Редагувати</a>
                        <form method="POST" action="index.php?route=recipe/delete" style="display:inline"
                              onsubmit="return confirm('Видалити рецепт?')">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
