<div class="page-home">
    <h1>PHP MVC Demo — LR5</h1>
    <p class="page-home__subtitle">Лабораторна робота №5 &mdash; Робота з файлами та базою даних (PDO)</p>
    <p class="text-muted">Розширення MVC-додатку з LR4: файлові операції, завантаження, каталоги, CRUD через PDO, авторизація.</p>

    <h2>Завдання 1: Робота з файлами</h2>
    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Гостьова книга</h3>
            <p class="card__text">
                Коментарі зберігаються у текстовому файлі (pipe-розділювач).
                Форма додавання + таблиця всіх записів.
            </p>
            <a href="index.php?route=guestbook/index" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Завантаження зображень</h3>
            <p class="card__text">
                Upload зображень на сервер. Перевірка типу та розміру.
                Галерея завантажених файлів.
            </p>
            <a href="index.php?route=upload/index" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Робота з каталогами</h3>
            <p class="card__text">
                Створення папки користувача з підпапками (video, music, photo).
                Видалення папки з усім вмістом.
            </p>
            <a href="index.php?route=folder/create" class="btn btn--small">Перейти</a>
        </div>
    </div>

    <h2>Завдання 2: База даних (PDO)</h2>
    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Каталог товарів (CRUD)</h3>
            <p class="card__text">
                Таблиця товарів з SQLite/MySQL через PDO.
                Додавання, редагування, видалення (prepared statements).
            </p>
            <a href="index.php?route=catalog/list" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Авторизація через БД</h3>
            <p class="card__text">
                Реєстрація, вхід, профіль, редагування, видалення акаунту.
                Хешування паролів (<code>password_hash</code>).
            </p>
            <a href="index.php?route=auth/login" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Сесії та Cookie</h3>
            <p class="card__text">
                Зміна кольору фону через сесію. Привітання через cookie.
                (Успадковано з LR4)
            </p>
            <a href="index.php?route=settings/color" class="btn btn--small">Перейти</a>
        </div>
    </div>

    <div class="info-block">
        <h2>Нові класи LR5</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Клас</th>
                    <th>Призначення</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>Database</code></td><td>PDO Singleton — підключення до БД (SQLite/MySQL)</td></tr>
                <tr><td><code>GuestbookController</code></td><td>Файлова гостьова книга (read/write)</td></tr>
                <tr><td><code>UploadController</code></td><td>Завантаження зображень на сервер</td></tr>
                <tr><td><code>FolderController</code></td><td>Створення/видалення каталогів користувачів</td></tr>
                <tr><td><code>CatalogController</code></td><td>CRUD товарів через PDO</td></tr>
                <tr><td><code>AuthController</code></td><td>Реєстрація, вхід, профіль, вихід</td></tr>
            </tbody>
        </table>
    </div>
</div>
