# PHP MVC Demo — Лабораторна робота №4-5

## Запуск

```bash
cd lr4/demo
php -S localhost:8080
```

Відкрити: [http://localhost:8080](http://localhost:8080)

## Маршрутизація

| URL | Сторінка |
|-----|----------|
| `index.php` | Головна |
| `index.php?route=regform/form` | Форма реєстрації |
| `index.php?route=regform/done` | Успішна реєстрація |
| `index.php?route=reqview/showrequest` | Перегляд GET/POST параметрів |
| `index.php?route=settings/color` | Колір фону (сесія) |
| `index.php?route=settings/greeting` | Привітання (cookie) |

## Структура

```
demo/
├── index.php                    # Точка входу
├── classes/                     # MVC-класи
│   ├── Application.php
│   ├── Router.php
│   ├── Request.php
│   ├── Controller.php
│   ├── PageController.php
│   ├── View.php
│   └── PageView.php
├── config/init.php              # Автозавантаження, сесія
├── controllers/                 # Контролери
│   ├── IndexController.php
│   ├── RegformController.php
│   ├── ReqviewController.php
│   └── SettingsController.php
├── views/                       # Шаблони
│   ├── layout/
│   ├── index/
│   ├── regform/
│   ├── reqview/
│   └── settings/
└── css/style.css
```
