<?php
$getParams = $getParams ?? [];
$postParams = $postParams ?? [];
$method = $method ?? 'GET';
?>

<h1>Перегляд параметрів запиту</h1>

<div class="reqview-grid">
    <div class="reqview-section">
        <h2>POST-форма</h2>
        <p>Надішліть POST-запит з довільними даними:</p>
        <form method="POST" action="index.php?route=reqview/showrequest&source=form" class="form">
            <div class="form__group">
                <label for="post_name" class="form__label">Ім'я</label>
                <input type="text" id="post_name" name="name" class="form__input" placeholder="Ваше ім'я">
            </div>
            <div class="form__group">
                <label for="post_message" class="form__label">Повідомлення</label>
                <textarea id="post_message" name="message" class="form__textarea" rows="3"
                          placeholder="Будь-який текст"></textarea>
            </div>
            <div class="form__group">
                <label for="post_number" class="form__label">Число</label>
                <input type="number" id="post_number" name="number" class="form__input" placeholder="42">
            </div>
            <button type="submit" class="btn">Надіслати POST</button>
        </form>

        <h3>GET-параметри в URL</h3>
        <p>Додайте параметри до URL, наприклад:</p>
        <code class="code-block">index.php?route=reqview/showrequest&name=John&age=25</code>
    </div>

    <div class="reqview-section">
        <h2>Результат</h2>
        <p><strong>Метод запиту:</strong> <code><?= htmlspecialchars($method) ?></code></p>

        <h3>GET-параметри</h3>
        <?php if (empty($getParams)): ?>
            <p class="text-muted">GET-параметрів немає. Додайте їх до URL.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr><th>Параметр</th><th>Значення</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($getParams as $key => $value): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($key) ?></code></td>
                            <td><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>POST-параметри</h3>
        <?php if (empty($postParams)): ?>
            <p class="text-muted">POST-параметрів немає. Надішліть форму зліва.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr><th>Параметр</th><th>Значення</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($postParams as $key => $value): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($key) ?></code></td>
                            <td><?= htmlspecialchars(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
