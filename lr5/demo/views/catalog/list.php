<?php
$products = $products ?? [];
?>

<h1>Каталог товарів</h1>
<p>CRUD через PDO (prepared statements). Дані зберігаються в <code>database/app.db</code> (SQLite).</p>

<div class="form__actions form__actions--mb">
    <a href="index.php?route=catalog/create" class="btn">Додати товар</a>
</div>

<?php if (empty($products)): ?>
    <p class="text-muted">Товарів ще немає.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Ціна</th>
                <th>Категорія</th>
                <th>Опис</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format((float)$p['price'], 2, '.', ' ') ?> грн</td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                    <td><?= htmlspecialchars($p['description']) ?></td>
                    <td class="table__actions">
                        <a href="index.php?route=catalog/edit&id=<?= (int)$p['id'] ?>" class="btn btn--small">Редагувати</a>
                        <form method="POST" action="index.php?route=catalog/delete" class="form--inline"
                              onsubmit="return confirm('Видалити товар?')">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn--small btn--danger">Видалити</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
