<div class="page-home">
    <h1>Кулінарний блог</h1>
    <p class="page-home__subtitle">Варіант 30 &mdash; Лабораторна робота №5</p>
    <p class="text-muted">Збірка українських рецептів. Файлова гостьова книга, галерея фото страв, CRUD рецептів через PDO, авторизація.</p>

    <h2>Файли</h2>
    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Гостьова книга</h3>
            <p class="card__text">Залишайте відгуки про рецепти. Коментарі зберігаються у текстовому файлі.</p>
            <a href="index.php?route=guestbook/index" class="btn btn--small">Відгуки</a>
        </div>

        <div class="card">
            <h3 class="card__title">Фото страв</h3>
            <p class="card__text">Завантажуйте фото приготовлених страв. Галерея кулінарних шедеврів.</p>
            <a href="index.php?route=upload/index" class="btn btn--small">Галерея</a>
        </div>

        <div class="card">
            <h3 class="card__title">Каталоги кухарів</h3>
            <p class="card__text">Персональні папки для кухарів з колекціями відео, музики та фото.</p>
            <a href="index.php?route=folder/create" class="btn btn--small">Каталоги</a>
        </div>
    </div>

    <h2>База даних</h2>
    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Рецепти (CRUD)</h3>
            <p class="card__text">Колекція рецептів з інгредієнтами, часом приготування та інструкціями. PDO + SQLite.</p>
            <a href="index.php?route=recipe/list" class="btn btn--small">До рецептів</a>
        </div>

        <div class="card">
            <h3 class="card__title">Акаунт кухаря</h3>
            <p class="card__text">Реєстрація, вхід, профіль. Хешування паролів, сесійна авторизація.</p>
            <a href="index.php?route=auth/login" class="btn btn--small">Увійти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Налаштування</h3>
            <p class="card__text">Колір фону (сесія) та привітання (cookie). Успадковано з ЛР4.</p>
            <a href="index.php?route=settings/color" class="btn btn--small">Налаштування</a>
        </div>
    </div>
</div>
