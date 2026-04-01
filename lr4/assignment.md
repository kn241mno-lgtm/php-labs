# Лабораторна робота №4

**Тема:** Створення MVC-додатку на PHP без використання фреймворків

## Теорія

[W3Schools PHP OOP](https://www.w3schools.com/php/php_oop_what_is.asp) + лекція по MVC

## Як виконувати

1. Подивіться робочий приклад: `demo/` — запустіть `php -S localhost:8000` і відкрийте `http://localhost:8000/lr4/demo/`
2. Створіть MVC-структуру у папці `lr4/` (дивіться секцію "Очікувана структура" нижче та `demo/` як приклад)

**Як перевірити:** запустіть `php -S localhost:8080` з папки `lr4/` і відкрийте `http://localhost:8080`

> Для MVC-додатку зручніше запускати сервер з папки лаби: `cd lr4 && php -S localhost:8080`

## Завдання 1. Структура MVC

Створити скелет веб-додатку з MVC. Сторінки:
- Головна (статична)
- Форма реєстрації
- Форма відправки POST-запиту
- Сторінка перегляду GET та POST параметрів
- Єдина точка входу (`index.php`)

Тема сайту = тема курсової роботи.

## Завдання 2. Модулі

### Модуль обробки форми

- Мінімум 7 полів, 4 різних видів
- Варіанти валідації за номером (1-24):
  - ПІБ тільки англ. символами
  - Паролі збігаються + E-mail формат
  - Стать + вік = обмеження реєстрації
  - ПІБ не порожні + макс. 3 уподобання
  - Калькулятор (sum/sub/div/mul/pow/sqrt)
  - Логін одне слово + пароль/логін >= 4 символи

### Модуль сесій

- Вибір кольору фону → збереження в сесії → застосування на всіх сторінках

### Модуль Cookie

- Форма: ім'я + стать → cookie
- На всіх сторінках: `"Вітаємо Вас, пане/пані ІМ'Я"`

## Паттерн MVC — Класи

| Клас | Призначення |
|------|-------------|
| `Application` | Конструктор + `Run()` |
| `Router` | Обробка URL → контролер + action |
| `Request` | Обгортка `$_GET` / `$_POST` |
| `Controller` | Базовий клас |
| `PageController` | Реалізація для сторінок |
| `View` | Базовий клас view |
| `PageView` | Шаблон сторінки + блоки |

## Роутинг

Два підходи:
1. **Динамічні адреси**: `index.php?route=regform/doreg`
2. **Віртуальні адреси**: `/regform/doreg.html` через `.htaccess` RewriteRule

## Іменування

- Контролер `regform` → клас `RegformController` → файл `controllers/RegformController.php`
- Методи дій: `action_ІМ'ЯДІЇ()`
- View: `views/<контролер>/<шаблон>.php`

## Очікувана структура

```
/
├── classes/
│   ├── Application.php
│   ├── Controller.php
│   ├── PageController.php
│   ├── Request.php
│   ├── Route.php
│   ├── View.php
│   └── PageView.php
├── config/
│   └── init.php
├── controllers/
│   ├── IndexController.php
│   └── RegformController.php
├── views/
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   ├── index/
│   │   └── main.php
│   ├── regform/
│   │   ├── form.php
│   │   └── done.php
│   └── reqview/
│       └── showrequest.php
├── index.php
└── .htaccess
```

## Корисні посилання

- [PHP OOP](https://www.w3schools.com/php/php_oop_what_is.asp) — що таке ООП
- [PHP Classes/Objects](https://www.w3schools.com/php/php_oop_classes_objects.asp) — класи та об'єкти
- [PHP Inheritance](https://www.w3schools.com/php/php_oop_inheritance.asp) — наслідування
- [PHP Abstract Classes](https://www.w3schools.com/php/php_oop_classes_abstract.asp) — абстрактні класи
- [PHP Static Methods](https://www.w3schools.com/php/php_oop_static_methods.asp) — статичні методи
- [PHP Sessions](https://www.w3schools.com/php/php_sessions.asp) — сесії
- [PHP Cookies](https://www.w3schools.com/php/php_cookies.asp) — cookies
- [PHP Form Handling](https://www.w3schools.com/php/php_form_handling.asp) — обробка форм
- [PHP Form Validation](https://www.w3schools.com/php/php_form_validation.asp) — валідація форм
- [MVC Pattern](https://developer.mozilla.org/en-US/docs/Glossary/MVC) — MVC (MDN)

## Здача

- Гілка `lr4` в репозиторії (див. [acceptance-criteria.md](../docs/acceptance-criteria.md))
- Push на GitHub
