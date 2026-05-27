# Курсова робота — головний індекс

> **Призначення папки `coursework/`:** повний набір матеріалів для виконання курсової роботи з дисципліни «Серверні технології та бекенд-розробка» (спеціальності 122 КН, 126 ІСТ, 1 курс, ЖДТУ).
>
> **Орієнтир — оцінка 5 (90–100 балів):** усі документи в папці написано саме під цю планку — щоб студент бачив, як виглядає робота рівня «відмінно».

---

## 📖 Як користуватись цим індексом

1. **Перший раз** → йди по секції **«Порядок роботи над курсовою»** нижче — це маршрут з 8 кроків від нульового стану до готового ПЗ.
2. **Шукаєш конкретну тему** → дивись секцію **«Структура папки»** — таблиця файлів з коротким описом кожного.
3. **Готуєшся до захисту ЛР4 (БД)** → секція **«Матеріали для ЛР4»**.
4. **Готуєшся до захисту курсової** → секція **«Матеріали для курсової»**.

---

## 🗂 Структура папки

### Категорії документів

Усі файли згруповано за роллю в процесі виконання роботи:

- 🎯 **Завдання**: що і як оцінюється.
    [assignment.md](assignment.md), [defense-checklist.md](defense-checklist.md)
- 🏗 **Проєктування**: як спроєктувати систему.
    [system-design.md](system-design.md), [functionality-flow.md](functionality-flow.md), [feature-catalog.md](feature-catalog.md), [routes-template.md](routes-template.md), [er-diagram-template.md](er-diagram-template.md), [theme-blueprints.md](theme-blueprints.md)
- 🗄 **База даних — реалізація**: як побудувати БД.
    [schemas.md](schemas.md), [er-diagrams.md](er-diagrams.md), [migrations-seeders-example.md](migrations-seeders-example.md)
- ✅ **База даних — якість**: як не втратити бали.
    [checklist-lr4.md](checklist-lr4.md), [typical-mistakes.md](typical-mistakes.md)
- 💬 **База даних — захист**: як пройти демонстрацію.
    [example-queries.md](example-queries.md)
- 🧩 **Шаблони реалізації**: що копіювати як старт.
    [code-patterns.md](code-patterns.md)
- 🛤 **Стратегія на курс**: як одну схему довести до курсової.
    [evolution-path.md](evolution-path.md)
- 📄 **Офіційна методичка**: джерело вимог.
    [Метод_реком_бекенд122.pdf](Метод_реком_бекенд122.pdf)

### Повна таблиця файлів

- [README.md](README.md): цей індекс.
- [assignment.md](assignment.md): темплейт курсової, стек, вимоги, критерії, дедлайни, захист.
- [defense-checklist.md](defense-checklist.md): чекліст перед захистом, сценарій демо, типові питання комісії.
- [theme-blueprints.md](theme-blueprints.md): готові blueprints для типових тем: магазин, booking, catalog, community, CRM.
- [schemas.md](schemas.md): центральний довідник БД з 7 еталонними схемами для 5 категорій варіантів.
- [er-diagrams.md](er-diagrams.md): Mermaid ER-діаграми для 7 референсних варіантів.
- [er-diagram-template.md](er-diagram-template.md): шаблони ER для Mermaid і DBDiagram.
- [checklist-lr4.md](checklist-lr4.md): чекліст самоперевірки ЛР4 і карта балів.
- [typical-mistakes.md](typical-mistakes.md): антипатерни та втрата балів.
- [example-queries.md](example-queries.md): SQL-запити для демонстрації на захисті.
- [evolution-path.md](evolution-path.md): як схема з ЛР4 росте в ЛР5, ЛР6 і курсову.
- [migrations-seeders-example.md](migrations-seeders-example.md): міграції і сідери для v1 Книгарня у vanilla PHP та Laravel.
- [feature-catalog.md](feature-catalog.md): каталог фіч по категоріях і рівнях складності.
- [functionality-flow.md](functionality-flow.md): юзер-флоу по категоріях систем.
- [system-design.md](system-design.md): архітектура рівня системи та обовʼязкові блоки коду.
- [routes-template.md](routes-template.md): шаблон документації маршрутів для ПЗ і README.
- [code-patterns.md](code-patterns.md): приклади Cart, Policy і FormRequest для Laravel і vanilla PHP.
- [Метод_реком_бекенд122.pdf](Метод_реком_бекенд122.pdf): офіційна методичка ЖДТУ з темами і прикладами схем.

**Разом:** 17 `.md` документів + 1 PDF методичка.

---

## 🛤 Порядок роботи над курсовою

Маршрут від нульового стану до готового ПЗ:

### Тиждень 1 — Розуміння і вибір

```text
1. assignment.md          → що треба здати, які бали, які варіанти
2. feature-catalog.md     → який функціонал вибрати (minimum/extended/advanced)
3. functionality-flow.md  → як користувач проходить сайт
```

### Тиждень 2 — Проєктування БД (ЛР4)

```text
4. schemas.md             → вибери еталон для своєї категорії (5 штук)
5. er-diagrams.md         → візуалізуй звʼязки (Mermaid)
6. checklist-lr4.md       → пройди чекліст перед захистом
7. typical-mistakes.md    → перевір що не повторюєш антипатерни
```

### Тиждень 3 — Реалізація БД

```text
8. migrations-seeders-example.md  → міграції + сідери (vanilla або Laravel)
9. example-queries.md             → SQL для демонстрації на захисті ЛР4
```

### Тиждень 4+ — Курсова

```text
10. system-design.md      → MVC архітектура, структура коду
11. evolution-path.md     → як доростити схему з ЛР4 до курсової
12. assignment.md         → остаточна звірка з вимогами перед здачею
```

---

## 🎓 Матеріали для ЛР4 (захист БД)

Все, що потрібно для захисту лабораторної №4 «Проєктування БД»:

- 🗺 **[schemas.md](schemas.md)** — вибери еталонну схему для своєї категорії
- 🔗 **[er-diagrams.md](er-diagrams.md)** — Mermaid ER-діаграми для візуалізації
- ✅ **[checklist-lr4.md](checklist-lr4.md)** — самоперевірка перед захистом
- ⚠️ **[typical-mistakes.md](typical-mistakes.md)** — помилки, які знімають бали
- 💾 **[migrations-seeders-example.md](migrations-seeders-example.md)** — робочі міграції + сідери
- 💬 **[example-queries.md](example-queries.md)** — SQL-запити для демонстрації викладачу

**Карта балів ЛР4:**

| Бали | Що реалізовано |
| ---- | -------------- |
| 60–74 | Базова схема 5–7 таблиць + 3 обовʼязкові діаграми |
| 75–89 | Базова + 3–4 розширення + Use Case |
| 90–100 | Повний еталон категорії: RBAC + audit_log + soft delete + індекси + UNIQUE + triggers + діаграма класів MVC |

---

## 🎓 Матеріали для курсової (захист ПЗ)

Все, що потрібно для повного ПЗ курсової:

- 📋 **[assignment.md](assignment.md)** — вимоги, структура, критерії
- 🎤 **[defense-checklist.md](defense-checklist.md)** — сценарій фінального показу і питання комісії
- 🏛 **[system-design.md](system-design.md)** — MVC архітектура, обовʼязкові блоки
- 🎯 **[feature-catalog.md](feature-catalog.md)** — вибір фіч під бажану оцінку
- 🌊 **[functionality-flow.md](functionality-flow.md)** — юзер-флоу сайту
- 🧱 **[theme-blueprints.md](theme-blueprints.md)** — готові набори сторінок, таблиць і ролей для типових тем
- 🧩 **[code-patterns.md](code-patterns.md)** — патерни Cart / Policy / FormRequest
- 🗺 **[routes-template.md](routes-template.md)** — шаблон документа `docs/routes.md`
- 🔧 **[er-diagram-template.md](er-diagram-template.md)** — шаблон ER для копіювання у Mermaid / DBDiagram
- 🛤 **[evolution-path.md](evolution-path.md)** — як ЛР4 → ЛР5 → ЛР6 складаються в курсову
- 📄 **[Метод_реком_бекенд122.pdf](Метод_реком_бекенд122.pdf)** — офіційні вимоги методички (Додатки З, И)

---

## 🔗 Як документи повʼязані між собою

```text
                    ┌─────────────────┐
                    │  assignment.md  │ ← точка входу
                    └────────┬────────┘
                             │
           ┌─────────────────┼─────────────────┐
           ▼                 ▼                 ▼
   ┌───────────────┐ ┌───────────────┐ ┌──────────────┐
   │ feature-      │ │ functionality-│ │ system-      │
   │ catalog.md    │ │ flow.md       │ │ design.md    │
   └───────┬───────┘ └───────┬───────┘ └──────┬───────┘
           │                 │                 │
           └─────────────────┼─────────────────┘
                             ▼
                    ┌─────────────────┐
                    │   schemas.md    │ ← довідник БД (7 еталонів)
                    └────────┬────────┘
                             │
        ┌────────────┬───────┼────────┬────────────┐
        ▼            ▼       ▼        ▼            ▼
   ┌─────────┐ ┌──────────┐ ┌─────┐ ┌──────────┐ ┌──────────────┐
   │ er-     │ │ checklist│ │typi-│ │ example- │ │ migrations-  │
   │ diagrams│ │ -lr4.md  │ │ cal-│ │ queries  │ │ seeders-     │
   │ .md     │ │          │ │mist.│ │ .md      │ │ example.md   │
   └─────────┘ └──────────┘ └─────┘ └──────────┘ └──────────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │evolution-path.md│ ← стратегія ЛР4→ЛР5→ЛР6→курсова
                    └─────────────────┘
```

**Читання:** зверху вниз. `assignment.md` — що робити. Далі — як проєктувати систему. Потім — як побудувати БД (основний блок). В кінці — як росте схема протягом курсу.

---

## 🗃 Звʼязок з рештою проєкту

Ця папка — не ізольована. Вона вбудована в репозиторій лаб:

```text
php-labs/
├── README.md                 ← загальний опис усіх лаб
├── docs/
│   ├── NAVIGATION.md         ← навігація ЛР1-ЛР6
│   └── STUDENT_GUIDE.md      ← гайд для студентів
├── lr1/ ... lr6/             ← самі лабораторні
└── coursework/               ← ЦЯ ПАПКА — матеріали курсової
    ├── README.md             ← ти тут
    └── ...
```

**Точки входу ззовні:**

- [../README.md](../README.md) — огляд усіх лаб курсу
- [../docs/NAVIGATION.md](../docs/NAVIGATION.md) — навігація по лабораторних
- [../docs/STUDENT_GUIDE.md](../docs/STUDENT_GUIDE.md) — студентський гайд

---

## 🎯 Принципи організації

**Single Source of Truth.** Кожна тема — в одному документі. Дублів немає. Якщо десь потрібна тема з іншого файлу — ставиться посилання, а не копіюється текст.

**Від загального до конкретного.** `assignment.md` — вимоги → `schemas.md` — довідник → `migrations-seeders-example.md` — код. Кожен наступний рівень конкретніший.

**Під кожен рівень оцінки — окремий контент.** Немає єдиного «мінімального прикладу» — є 60/75/90+ варіанти, щоб студент бачив різницю.

**Антипатерни поруч з еталонами.** Завжди показано не тільки «як треба», а й «як не треба і скільки балів втрачають».

**Повна робоча реалізація.** `migrations-seeders-example.md` і `example-queries.md` — не псевдокод. Їх можна скопіювати і запустити.

---

*Індекс оновлюється при додаванні/видаленні документів. Якщо додаєш новий файл — додай рядок у «Повну таблицю файлів» вище і в відповідну категорію.*
