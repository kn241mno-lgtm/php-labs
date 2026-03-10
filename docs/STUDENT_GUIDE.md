# Інструкція для студентів

> Цей посібник написаний для **першокурсників**, які щойно почали вивчати програмування. Якщо ви ніколи не працювали з Git, терміналом або PHP — це нормально. Все пояснено крок за кроком, з аналогіями зі знайомих речей.

## Словник

| Термін | Що це | Аналогія |
| ------ | ----- | -------- |
| **upstream** | Репозиторій викладача (оригінал) | Підручник у бібліотеці |
| **origin** | Ваша копія на GitHub (форк) | Ваша ксерокопія підручника |
| **local** | Копія на вашому комп'ютері | Підручник на вашому столі |
| **fork** | Створити свою копію репо на GitHub | Зробити ксерокопію |
| **clone** | Скачати репо з GitHub на комп'ютер | Забрати ксерокопію додому |
| **branch** | Гілка — окрема лінія роботи | Закладка для кожної лаби |
| **commit** | Зберегти зміни в Git | Зберегти прогрес у грі |
| **push** | Відправити зміни з комп'ютера на GitHub | Вивантажити в хмару |
| **merge** | Об'єднати зміни з однієї гілки в іншу | Скопіювати нові сторінки з підручника |
| **fetch** | Перевірити чи є оновлення (без застосування) | Подивитись чи є нова версія |

---

## Схема роботи

```text
  upstream (victorchei/php-labs)          ← репозиторій ВИКЛАДАЧА
          │
          │  fork (один раз)
          ▼
  origin (ВАШ_ЛОГІН/php-labs)            ← ваша копія на GitHub
          │
          │  clone (один раз)
          ▼
  local (папка php-labs на комп'ютері)   ← тут ви працюєте
          │
          ├── main          ← головна гілка (оновлення від викладача)
          ├── lr1           ← ваша робота над ЛР1
          ├── lr2           ← ваша робота над ЛР2
          └── ...
```

---

## Початкове налаштування (один раз)

### Крок 0: Встановіть PHP та Git

Якщо PHP та Git ще не встановлені — див. [setup/README.md](../setup/README.md).

### Крок 1: Налаштуйте Git (ім'я та email)

```bash
git config --global user.name "Прізвище Ім'я"
git config --global user.email "your.email@ztu.edu.ua"
```

> Це потрібно зробити **один раз**. Без цього коміти будуть анонімними.

### Крок 2: Fork та Clone

> Для цього кроку потрібен Git. Якщо `git --version` не працює — поверніться до [Кроку 0](#крок-0-встановіть-php-та-git).

1. Відкрийте: <https://github.com/victorchei/php-labs>
2. Натисніть **Fork** (правий верхній кут) — це створить копію у вашому акаунті
3. Clone свій форк:

```bash
git clone https://github.com/ВАШ_ЛОГІН/php-labs.git
cd php-labs
git remote add upstream https://github.com/victorchei/php-labs.git
```

> `upstream` — це зв'язок з репозиторієм викладача. Потрібен щоб отримувати оновлення.

### Крок 3: Запустіть сервер

```bash
php -S localhost:8000
```

Відкрийте `http://localhost:8000` — оберіть свій варіант.

---

## Як переглянути demo

Demo — це робочий приклад реалізації лабораторної (з іншими даними, ніж у вашому варіанті).

```bash
php -S localhost:8000
```

Demo доступні за адресами:
- `http://localhost:8000/lr1/demo/` — ЛР1: Базові конструкції PHP
- `http://localhost:8000/lr2/demo/` — ЛР2: Масиви та рядки
- `http://localhost:8000/lr3/demo/` — ЛР3: ООП
- `http://localhost:8000/lr4/demo/` — ЛР4: MVC

Також є **повністю розв'язані варіанти 30**:
- `http://localhost:8000/lr1/variants/v30/` — ЛР1 (7 завдань)
- `http://localhost:8000/lr2/variants/v30/` — ЛР2 (11 завдань)
- `http://localhost:8000/lr3/variants/v30/` — ЛР3 (4 завдання)

Це приклади того, як має виглядати виконана лабораторна.

---

## Як працювати над завданням

### 1. Створіть гілку для лабораторної

Для кожної лабораторної — **окрема гілка**. Це обов'язково (див. [критерії прийняття](acceptance-criteria.md)).

```bash
git checkout -b lr1
```

> Ви зараз на гілці `lr1`. Всі зміни зберігатимуться тут, не зачіпаючи `main`.

### 2. Виконуйте завдання

1. Прочитайте `lr1/assignment.md` — загальний опис лабораторної
2. Відкрийте свій варіант: `lr1/variants/vN.md` (де N — ваш номер)
3. Подивіться **demo/** — робочий приклад (з іншими даними)
4. Подивіться **variants/v30/** — повністю розв'язаний варіант 30

**Де створювати файли (ЛР1):**

Скопіюйте папку `v30/` → перейменуйте в `vN/` (де N — ваш номер варіанту) → замініть дані.

```text
lr1/
├── demo/               ← НЕ ЧІПАТИ (приклад від викладача)
├── variants/
│   ├── v1.md ... v30.md  ← завдання (НЕ ЧІПАТИ)
│   ├── v30/            ← повний приклад (НЕ ЧІПАТИ)
│   └── vN/             ← ← ← ВАША ПАПКА
│       ├── index.php       ← завдання 1: перша програма
│       ├── task2.php       ← завдання 2: конвертер валют
│       ├── task3.php       ← завдання 3: сезон
│       ├── task4.php       ← завдання 4: голосний/приголосний
│       └── ...
```

> Нумерація: завдання 1 = `index.php`, завдання 2 = `task2.php`, і так далі. Структуру файлів беріть з `v30/`.

### 3. Перевірте у браузері

```bash
php -S localhost:8000
```

Відкрийте: `http://localhost:8000/lr1/variants/vN/` (де N — ваш номер)

### 4. Збережіть та відправте

Після кожного завдання:

```bash
git add lr1/variants/vN/task2.php
git commit -m "feat: task2 - formatted text output"
```

Коли готові відправити на GitHub:

```bash
git push -u origin lr1
```

> `-u origin lr1` потрібно тільки при **першому** push гілки. Потім достатньо `git push`.

### 5. Наступна лабораторна

```bash
git checkout main
git checkout -b lr2
```

```text
РОБОЧИЙ ПРОЦЕС:

1. git checkout -b lrN                  (створити гілку)
2. Прочитай assignment.md               (загальний опис)
3. Відкрий vN.md                        (завдання варіанту)
4. Подивись demo/ та v30/               (приклади)
5. ЛР1: скопіюй v30/ → vN/             (див. v30/README.md)
   ЛР2-4: створи файли в lrN/          (за прикладом з demo/)
6. Заміни дані на свої з vN.md
7. Перевір у браузері
8. git add lr1/variants/vN/файл.php     (або lrN/файл.php)
9. git commit -m "feat: taskN - опис"
10. git push -u origin lrN
```

---

## Оновлення від викладача

Коли викладач додає нові лабораторні, оновлює demo або виправляє помилки — потрібно синхронізувати.

**Коли оновлювати:** перед початком кожної нової лабораторної.

**Що змінилось:** дивіться [CHANGELOG.md](../CHANGELOG.md) — там описано всі зміни, які впливають на студентів.

```text
upstream (викладач) ──fetch──→ local main ──merge──→ local lr2
                                    │
                                    push
                                    ↓
                              origin (GitHub)
```

### Крок 1: Оновіть main

Спочатку збережіть свою поточну роботу (якщо є незакомічені зміни):

```bash
git add .
git commit -m "wip: зберігаю поточну роботу"
```

Потім оновіть main:

```bash
git checkout main
git fetch upstream
git merge upstream/main
git push
```

> **Що тут відбувається:**
> 1. `checkout main` — переключитись на головну гілку
> 2. `fetch upstream` — перевірити оновлення у викладача
> 3. `merge upstream/main` — додати оновлення в ваш main
> 4. `push` — відправити оновлений main на ваш GitHub

### Крок 2: Оновіть робочу гілку

Якщо ви вже працюєте над лабою і хочете отримати оновлення:

```bash
git checkout lr2
git merge main
```

> Це додасть нові файли від викладача (demo, shared) у вашу робочу гілку.

---

## Типові ситуації

### "Маю незбережені зміни, Git не дає переключити гілку"

```text
error: Your local changes to the following files would be overwritten...
```

**Рішення:** збережіть зміни перед переключенням:

```bash
git add .
git commit -m "wip: зберігаю поточну роботу"
git checkout main
```

### "Виникає конфлікт при merge"

```text
CONFLICT (content): Merge conflict in shared/css/base.css
```

**Що сталося:** ви випадково змінили файл, який також змінив викладач.

**Рішення:** прийміть версію викладача:

```bash
git checkout --theirs shared/css/base.css
git add shared/css/base.css
git commit -m "fix: resolve conflict, keep upstream version"
```

> **Правило:** файли в `shared/`, `demo/`, чужі варіанти — завжди версія викладача.

### "upstream не знайдено"

```text
fatal: 'upstream' does not appear to be a git repository
```

**Рішення:** додайте зв'язок:

```bash
git remote add upstream https://github.com/victorchei/php-labs.git
```

### "Я працюю тільки в main, не створював гілки"

**Рішення:** створіть гілку з поточного стану:

```bash
git checkout -b lr1
git push -u origin lr1
```

> Це створить гілку `lr1` з усіма вашими змінами. Main залишиться як був.

### "Випадково push в upstream замість origin"

```text
remote: Permission to victorchei/php-labs.git denied
```

Це нормально — у вас немає прав на репо викладача. Ваші зміни нікуди не пішли. Просто push в origin:

```bash
git push -u origin lr1
```

### "Викладач додав нову лабу, але я її не бачу"

**Причина:** ваш локальний main застарів.

**Рішення:** оновіть main і створіть нову гілку:

```bash
git checkout main
git fetch upstream
git merge upstream/main
git push
git checkout -b lr3
```

---

## Уникнення конфліктів

- Працюйте **ТІЛЬКИ** зі своїм варіантом
- **Не змінюйте:** `shared/`, `demo/`, `docs/`, інші варіанти
- Створюйте **окрему гілку** для кожної лаби
- **ЛР1:** файли створюйте в `lr1/variants/vN/` (скопіюйте v30/ як шаблон). **ЛР2-4:** файли створюйте в `lrN/` (за прикладом з demo/)
- Не чіпайте demo/, v30/, чужі варіанти

---

## Структура проєкту

```text
php-labs/
├── shared/          # Спільні стилі (НЕ ЗМІНЮВАТИ)
├── docs/            # Документація
├── lr1/
│   ├── demo/        # Робочий приклад (дивитись, НЕ ЗМІНЮВАТИ)
│   ├── variants/
│   │   ├── v1.md    # Завдання для варіанту 1
│   │   ├── v30/     # Повністю розв'язаний варіант 30 (приклад)
│   │   ├── v30.md
│   │   └── vN/      # ← ТУТ ваш код (скопіюйте v30/, перейменуйте)
│   │       ├── index.php
│   │       ├── task2.php
│   │       └── ...
│   └── assignment.md # Загальний опис лаби
├── lr2/
│   ├── demo/        # Робочий приклад
│   ├── variants/
│   │   ├── v1.md ... v30.md
│   │   └── v30/     # Повністю розв'язаний варіант 30
│   └── assignment.md
├── lr3/, lr4/       # Файли створюйте в lr3/, lr4/
```

---

## Типові помилки

| Проблема | Рішення |
| -------- | ------- |
| `php` не знайдено | Встановіть PHP: [setup/README.md](../setup/README.md) |
| `git` не знайдено | Встановіть Git: [setup/README.md](../setup/README.md) |
| `permission denied` | Перевірте SSH ключі або використовуйте HTTPS |
| Сторінка не працює | Перевірте синтаксис: `php -l file.php` |
| Сервер не запускається | Перевірте PHP: `php -v`. Порт зайнятий? Спробуйте `php -S localhost:8080` |
| `upstream` не знайдено | `git remote add upstream https://github.com/victorchei/php-labs.git` |
| Коміт з неправильним автором | Налаштуйте: `git config --global user.name "Ім'я"` |

---

## Рекомендований редактор

Встановіть [Visual Studio Code](https://code.visualstudio.com/) (безкоштовний). Корисні розширення:

- **PHP Intelephense** — підказки та автодоповнення для PHP
- **GitLens** — зручна робота з Git прямо в редакторі

Як відкрити проєкт: File → Open Folder → оберіть папку `php-labs`.

Вбудований термінал: **Ctrl + `** (або меню Terminal → New Terminal) — тут запускаєте `php -S localhost:8000`.

---

## Навчальні матеріали

| Тема | Посилання |
|------|-----------|
| PHP Tutorial | [W3Schools PHP](https://www.w3schools.com/php/) |
| PHP Reference | [PHP.net Manual](https://www.php.net/manual/en/) |
| HTML/CSS | [W3Schools HTML](https://www.w3schools.com/html/), [W3Schools CSS](https://www.w3schools.com/css/) |
| Git | [Git довідник](git-guide.md) |
| Laravel | [Laravel Docs](https://laravel.com/docs) |

> Використовуйте англомовні джерела (W3Schools, MDN, php.net). Російськомовні сайти (habr.com, metanit.com тощо) — не рекомендовані.

---

## Корисні посилання

- [Налаштування середовища](../setup/README.md)
- [Критерії прийняття](acceptance-criteria.md)
- [Git довідник](git-guide.md)
- [Запуск проєкту](running-project.md)
- [Проблеми на Windows](../troubleshooting/windows.md)

---

## Для викладача

<details>
<summary>Що оновлюється автоматично</summary>

| Що оновлюється | Студент отримає |
| -------------- | --------------- |
| `shared/` | Так |
| `demo/` | Так |
| `docs/` | Так |
| `lr2/`, `lr3/` (нові лаби) | Так |
| Нові варіанти | Так |

</details>

<details>
<summary>Перевірка робіт</summary>

1. Студент надсилає посилання на свій форк
2. Викладач клонує/переглядає форк
3. Перевіряє гілку `lrN` — порівнює з demo/ та vN.md

</details>

<details>
<summary>Процес випуску оновлень</summary>

1. Внесіть зміни в demo/, shared/, docs/ або variants/
2. Оновіть `CHANGELOG.md` — опишіть що змінилось
3. Створіть тег: `git tag lr2-v1.1 -m "LR2: fix task5 password validation"`
4. Push: `git push && git push --tags`

**Формат тегів:** `lrN-vX.Y`
- `lr1-v1.0` — перший реліз ЛР1
- `lr1-v1.1` — виправлення/покращення ЛР1
- `lr2-v1.0` — перший реліз ЛР2

**CHANGELOG:** кожне оновлення = новий запис. Студенти перевіряють CHANGELOG перед `git fetch upstream`.

</details>
