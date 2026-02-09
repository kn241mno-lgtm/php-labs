# php-labs v1 — Архітектура (archived)

> Документація попередньої версії проекту. Проект перейшов на v2 (demo + 30 ТЗ).

## Підхід

TDD-подібний: викладач створює demo (робочий приклад) + тести, студент отримує стаби (порожні функції) і має зробити тести зеленими.

## Структура

```
lrN/
├── demo/
│   ├── index.php          # Головна сторінка demo
│   ├── tasks/             # Реалізовані функції (еталон)
│   ├── tests/             # Тести (масив test cases)
│   ├── run_tests.php      # Test runner (CLI)
│   └── taskN.php          # Display pages
├── variants/
│   ├── shared/            # Спільний layout для варіантів
│   │   └── layout.php
│   ├── v1/
│   │   ├── config.php     # Метадані варіанту
│   │   ├── tasks/         # Стаби (return 0, "", null)
│   │   ├── tests/         # Тести з унікальними expected values
│   │   └── run_tests.php  # Test runner для варіанту
│   └── v2/ ...
├── validate_lab.php       # Перевірка всіх варіантів
└── assignment.md          # Опис лаби
```

## Test Runner

`run_tests.php` — CLI-скрипт:
1. `require` файл з функцією
2. `call_user_func_array($functionName, $input)`
3. Порівняння з `expected` або виклик `validator` callback
4. Кольоровий вивід: ✓ зелений / ✗ червоний

Формат тестів:
```php
return [
    'functionName' => [
        ['name' => 'Опис', 'input' => [...], 'expected' => value],
        ['name' => 'Опис', 'input' => [...], 'validator' => fn($result) => bool],
    ],
];
```

## Стаби (variants)

Кожен варіант мав стаби — функції що повертають порожні значення:
- `return 0` для числових
- `return ""` для рядкових
- `return null` / `return false`

Очікувана поведінка: demo тести PASS, variant тести FAIL (0%).

## Чому відмовились

1. **Тести працюють тільки для чистих функцій** (LR1-LR3)
2. **LR4-5** (MVC) — Router, Controller потребують HTTP-сервер
3. **LR6** (Laravel) — інший стек, свій тестовий фреймворк
4. **Підтримка стабів** — кожен варіант потребує окремого коду
5. **Єдиний підхід неможливий** для всіх 6 лабораторних
