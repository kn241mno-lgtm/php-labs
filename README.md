# php-labs

Лабораторні роботи з курсу "Серверні технології та бекенд-розробка" для студентів спеціальностей КН та ІСТ Державного університету «Житомирська політехніка».

> **Для першокурсників:** Проєкт орієнтований на студентів, які щойно почали вивчати програмування. Документація написана з розрахунку на нульовий досвід — крок за кроком, з поясненнями кожного терміну.

**Курс:** [learn.ztu.edu.ua](https://learn.ztu.edu.ua/course/view.php?id=7082)

## Викладач

**Желізко Віктор Вікторович** — асистент кафедри комп'ютерних наук

- Email: <kkn_zhvv@ztu.edu.ua>
- [Профіль викладача](https://ztu.edu.ua/teacher/445.html)
- [LinkedIn](https://www.linkedin.com/in/viktorzhelizko/)
- [ORCID](https://orcid.org/0009-0001-4178-6631)

## Швидкий старт (для студентів)

> **Повна інструкція:** [docs/STUDENT_GUIDE.md](docs/STUDENT_GUIDE.md)

1. **Встановіть редактор** — рекомендуємо [Visual Studio Code](https://code.visualstudio.com/) (безкоштовний). Розширення: PHP Intelephense, GitLens
2. **Налаштуйте середовище** — встановіть PHP та Git: [setup/README.md](setup/README.md)
3. **Fork** цей репозиторій (кнопка Fork на GitHub)
4. **Clone** свій форк:

```bash
git clone https://github.com/ВАШ_ЛОГІН/php-labs.git
cd php-labs
```

5. **Додайте upstream** (для синхронізації):

```bash
git remote add upstream https://github.com/victorchei/php-labs.git
```

6. **Запустіть сервер:**

```bash
php -S localhost:8000
```

Відкрийте: <http://localhost:8000>

## Приклад виконання (Варіант 30)

Папки `lrN/variants/v30/` містять повністю розв'язаний варіант 30 — це **приклад** того, як має виглядати виконана лабораторна. **Не видаляйте ці папки** — використовуйте як зразок разом з `demo/`.

```bash
php -S localhost:8000
# http://localhost:8000/lr1/variants/v30/ — ЛР1 (7 завдань)
# http://localhost:8000/lr2/variants/v30/ — ЛР2 (11 завдань)
# http://localhost:8000/lr3/variants/v30/ — ЛР3 (4 завдання)
# http://localhost:8000/lr4/variants/v30/ — ЛР4 (MVC, сесії, форми)
# http://localhost:8000/lr5/variants/v30/ — ЛР5 (файли, PDO, CRUD)
```

## Як отримати оновлення від викладача

Коли викладач додає нові лабораторні, оновлює demo або виправляє помилки — синхронізуйте свій форк:

```bash
git fetch upstream
git checkout main
git merge upstream/main
git push
```

> Якщо виникає конфлікт — переконайтесь, що ви не змінювали файли в `shared/`, `demo/` або чужі варіанти. Детальніше: [docs/git-guide.md](docs/git-guide.md)

## Лабораторні роботи

1. Базові конструкції мови PHP
2. Функції, рядки, масиви, форми
3. Об'єктно-орієнтоване програмування
4. MVC паттерн
5. Файли та база даних (PDO)
6. Laravel
7. Laravel (продовження)

## Курсова робота

Вимоги, структура ПЗ, критерії оцінювання — [coursework/assignment.md](coursework/assignment.md).

## Відомість з оцінками

[Google Таблиця](https://docs.google.com/spreadsheets/d/1M2Ufe3UGO_XIDvcZdrFz9lf5TwFHi6Ei/edit?gid=1868485203#gid=1868485203)

## Критерії прийняття

- [Структура репозиторію](docs/acceptance-criteria.md#структура-репозиторію)
- [Неймінг гілок](docs/acceptance-criteria.md#неймінг-гілок)
- [Вимоги до комітів](docs/acceptance-criteria.md#вимоги-до-комітів)
- [Формат коміт-повідомлень](docs/acceptance-criteria.md#формат-коміт-повідомлень)

## Документація

- [Інструкція для студентів](docs/STUDENT_GUIDE.md) ⭐
- [Запуск проєкту](docs/running-project.md)
- [Інструкція з роботи з Git](docs/git-guide.md)
- [Налаштування середовища](setup/README.md)
- [Курсова робота — вимоги](coursework/assignment.md)
