<div class="page-home">
    <h1>PHP MVC Demo</h1>
    <p class="page-home__subtitle">Лабораторна робота №4-5 &mdash; Створення MVC-додатку на PHP</p>

    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Структура MVC</h3>
            <p class="card__text">
                Додаток побудований за паттерном Model-View-Controller.
                Єдина точка входу <code>index.php</code>, маршрутизація через
                <code>?route=controller/action</code>.
            </p>
        </div>

        <div class="card">
            <h3 class="card__title">Реєстрація</h3>
            <p class="card__text">
                Форма з 9 полями та 4 різними типами вводу.
                Валідація: англійські імена, збіг паролів, формат E-mail.
            </p>
            <a href="index.php?route=regform/form" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Параметри запиту</h3>
            <p class="card__text">
                Перегляд GET та POST параметрів. Форма для надсилання
                POST-запиту з довільними даними.
            </p>
            <a href="index.php?route=reqview/showrequest" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Сесії та Cookie</h3>
            <p class="card__text">
                Зміна кольору фону через сесію. Привітання користувача
                через cookie на всіх сторінках.
            </p>
            <a href="index.php?route=settings/color" class="btn btn--small">Перейти</a>
        </div>
    </div>

    <div class="info-block">
        <h2>Класи MVC</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Клас</th>
                    <th>Призначення</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>Application</code></td><td>Завантаження додатку, виклик контролера</td></tr>
                <tr><td><code>Router</code></td><td>Розбір URL &rarr; контролер + дія</td></tr>
                <tr><td><code>Request</code></td><td>Обгортка для <code>$_GET</code> / <code>$_POST</code></td></tr>
                <tr><td><code>Controller</code></td><td>Базовий клас контролера</td></tr>
                <tr><td><code>PageController</code></td><td>Контролер для сторінок</td></tr>
                <tr><td><code>View</code></td><td>Базовий клас для відображення</td></tr>
                <tr><td><code>PageView</code></td><td>Шаблон сторінки з layout</td></tr>
            </tbody>
        </table>
    </div>
</div>
