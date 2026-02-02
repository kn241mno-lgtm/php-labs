# php-labs

Лабораторні роботи з курсу "Серверні технології та бекенд-розробка" для студентів спеціальностей КН та ІСТ Державного університету «Житомирська політехніка».

**Курс:** [learn.ztu.edu.ua](https://learn.ztu.edu.ua/course/view.php?id=7082)

## Викладач

**Желізко Віктор Вікторович** — асистент кафедри комп'ютерних наук

- Email: <kkn_zhvv@ztu.edu.ua>
- [Профіль викладача](https://ztu.edu.ua/teacher/445.html)
- [ORCID](https://orcid.org/0009-0001-4178-6631)

## Швидкий старт

Склонуйте репозиторій:

```bash
git clone https://github.com/victorchei/php-labs.git
cd php-labs
```

Детальні інструкції встановлення для Windows, macOS та Linux: [setup/README.md](setup/README.md)

## Лабораторні роботи

1. Базові конструкції мови PHP
2. Функції, рядки, масиви, форми
3. Об'єктно-орієнтоване програмування
4. MVC паттерн
5. MVC паттерн (продовження)
6. Laravel
7. Laravel (продовження)

## Критерії прийняття

> **Git:** Детальна інструкція з роботи з Git — [GIT_GUIDE.md](GIT_GUIDE.md)

### Структура репозиторію

- **Один репозиторій** на всі лабораторні роботи
- Кожна лабораторна виконується в **окремій гілці**
- Гілки **не видаляються** після здачі

### Неймінг гілок

| Формат     | Приклад             | Опис                         |
| ---------- | ------------------- | ---------------------------- |
| `lr<N>`    | `lr1`, `lr2`, `lr3` | Рекомендований формат        |
| `lab<N>`   | `lab1`, `lab2`      | Альтернативний варіант       |
| `Back-End` | `Back-End`          | Для здачі (якщо вимагається) |

```bash
# Створити нову гілку для ЛР1
git checkout -b lr1

# Переключитися на існуючу гілку
git checkout lr2

# Переглянути всі гілки
git branch -a
```

### Вимоги до комітів

- Мінімум **1 коміт на кожне завдання** лабораторної роботи
- Коміти повинні мати **осмислені повідомлення** (не "fix", "update", "changes")
- **Правильні налаштування Git** — ім'я та email мають відповідати університетському акаунту:

```bash
# Перевірити поточні налаштування
git config user.name
git config user.email

# Налаштувати для цього репозиторію
git config user.name "Прізвище Ім'я"
git config user.email "your.email@ztu.edu.ua"
```

### Формат коміт-повідомлень

Використовуйте [Conventional Commits](https://www.conventionalcommits.org/):

```text
<type>: <short description>
```

| Тип        | Опис                             |
| ---------- | -------------------------------- |
| `feat`     | Нова функціональність            |
| `fix`      | Виправлення помилки              |
| `docs`     | Документація                     |
| `style`    | Форматування (не впливає на код) |
| `refactor` | Рефакторинг коду                 |
| `test`     | Додавання тестів                 |

### Приклад історії комітів

```text
feat: task7 - generate table and random squares
feat: task6 - three-digit number operations
feat: task5 - character classification with switch
feat: task4 - season detection with if-else
feat: task3 - currency converter
feat: task2 - formatted text output
docs: task1 - environment setup
```
