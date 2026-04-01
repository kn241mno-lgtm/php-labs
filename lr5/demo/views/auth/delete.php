<h1>Видалення акаунту</h1>

<div class="alert alert--error">
    <strong>Увага!</strong> Ця дія незворотня. Ваш акаунт та всі дані будуть видалені назавжди.
</div>

<form method="POST" action="index.php?route=auth/delete" class="form">
    <div class="form__actions">
        <button type="submit" class="btn btn--danger"
                onclick="return confirm('Ви впевнені? Акаунт буде видалено назавжди.')">
            Так, видалити мій акаунт
        </button>
        <a href="index.php?route=auth/profile" class="btn btn--secondary">Скасувати</a>
    </div>
</form>
