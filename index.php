<?php
/**
 * Home Page - Variant Selection
 * After selecting variant → show info page with links to demo & assignment
 */

// Dev reload support
require_once __DIR__ . '/shared/helpers/dev_reload.php';
handleDevReloadRequest();

$variants = [];
for ($i = 1; $i <= 30; $i++) {
    $variants["v{$i}"] = "Варіант {$i}";
}

$selectedVariant = $_GET['variant'] ?? '';
$variantNumber = 0;
if ($selectedVariant && preg_match('/^v(\d+)$/', $selectedVariant, $m)) {
    $variantNumber = (int)$m[1];
}

// Labs configuration
$labs = [
    'lr1' => 'Лабораторна 1 — Базові конструкції PHP',
    'lr2' => 'Лабораторна 2 — Масиви та рядки',
    'lr3' => 'Лабораторна 3 — ООП',
    'lr4' => 'Лабораторна 4 — MVC',
    'lr6' => 'Лабораторна 6 — Laravel',
];
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>PHP Labs — <?= $variantNumber ? "Варіант {$variantNumber}" : 'Вибір варіанту' ?></title>
    <link rel="stylesheet" href="shared/css/base.css">
    <style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0e7ff 0%, #f3f4f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .main-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.1);
        padding: 50px 40px;
        text-align: center;
        max-width: 600px;
        width: 100%;
    }

    h1 {
        margin: 0 0 10px 0;
        color: #1f2937;
        font-size: 32px;
    }

    .subtitle {
        color: #6b7280;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .variant-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    select {
        font-size: 18px;
        padding: 14px 20px;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    select:focus {
        outline: none;
        border-color: #6366f1;
    }

    .btn-go {
        font-size: 18px;
        padding: 14px 24px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-go:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #6366f1;
        text-decoration: none;
        font-size: 14px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .lab-list {
        text-align: left;
        margin-top: 20px;
    }

    .lab-item {
        background: #f8fafc;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 12px;
        border: 1px solid #e2e8f0;
    }

    .lab-item h3 {
        margin: 0 0 8px 0;
        color: #1f2937;
        font-size: 16px;
    }

    .lab-links {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .lab-links a {
        font-size: 14px;
        color: #6366f1;
        text-decoration: none;
        padding: 4px 12px;
        border-radius: 6px;
        background: #eef2ff;
        transition: background 0.2s;
    }

    .lab-links a:hover {
        background: #e0e7ff;
    }

    .lab-links .no-demo {
        font-size: 14px;
        color: #9ca3af;
        padding: 4px 12px;
    }
    </style>
</head>

<body>
    <div class="main-card">
        <?php if ($variantNumber >= 1 && $variantNumber <= 30): ?>
            <a href="index.php" class="back-link">&larr; Назад до вибору варіанту</a>
            <h1>Варіант <?= $variantNumber ?></h1>
            <p class="subtitle">Лабораторні роботи для вашого варіанту</p>

            <div class="lab-list">
                <?php foreach ($labs as $labDir => $labName): ?>
                    <?php
                    $hasDemo = file_exists(__DIR__ . "/{$labDir}/demo/index.php");
                    $hasAssignment = file_exists(__DIR__ . "/{$labDir}/assignment.md");
                    $hasVariant = file_exists(__DIR__ . "/{$labDir}/variants/v{$variantNumber}.md");
                    ?>
                    <div class="lab-item">
                        <h3><?= htmlspecialchars($labName) ?></h3>
                        <div class="lab-links">
                            <?php if ($hasDemo): ?>
                                <a href="<?= $labDir ?>/demo/">Demo</a>
                            <?php else: ?>
                                <span class="no-demo">Demo недоступне</span>
                            <?php endif; ?>

                            <?php if ($hasVariant): ?>
                                <a href="<?= $labDir ?>/variants/v<?= $variantNumber ?>.md">Завдання (v<?= $variantNumber ?>.md)</a>
                            <?php endif; ?>

                            <?php if ($hasAssignment): ?>
                                <a href="<?= $labDir ?>/assignment.md">Опис лаби</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <h1>PHP Labs</h1>
            <p class="subtitle">Серверні технології — 30 варіантів</p>

            <form method="get" class="variant-form">
                <select name="variant" required>
                    <option value="">Оберіть свій варіант...</option>
                    <?php foreach ($variants as $key => $name): ?>
                    <option value="<?= $key ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-go">Перейти</button>
            </form>
        <?php endif; ?>
    </div>
    <?= devReloadScript() ?>
</body>

</html>
