<?php
/**
 * Завдання 7.1: Кольорова таблиця n×n
 *
 * Демонстрація: цикли for, функції, генерація HTML/CSS
 */
require_once __DIR__ . '/layout.php';

/**
 * Генерує HTML таблицю n×n з випадковими кольорами
 */
function generateColorTable(int $n): string
{
    $html = "<table class='chessboard'>";
    for ($i = 0; $i < $n; $i++) {
        $html .= "<tr>";
        for ($j = 0; $j < $n; $j++) {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            $html .= "<td style='background-color:$color;'></td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

// Параметри (demo)
$n = 5;

// Генеруємо таблицю
$table = generateColorTable($n);

$content = '
    <h1>🎨 Кольорова таблиця ' . $n . '×' . $n . '</h1>
    <div class="params">generateColorTable(' . $n . ')</div>
    ' . $table . '
    <p class="info" style="color:rgba(255,255,255,0.8);margin-top:20px;">Оновіть сторінку для нових кольорів 🔄</p>';

renderDemoLayout($content, 'Завдання 7.1', 'task7-table-body');
