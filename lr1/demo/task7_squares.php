<?php
/**
 * Завдання 7.2: Випадкові червоні квадрати на чорному тлі
 *
 * Демонстрація: цикли, функції, CSS positioning, mt_rand()
 */
require_once __DIR__ . '/layout.php';

/**
 * Генерує HTML з випадковими квадратами
 */
function generateRandomSquares(int $n): string
{
    $html = "<div style='position:relative;width:100vw;height:100vh;background:black;'>";

    for ($i = 0; $i < $n; $i++) {
        $size = mt_rand(20, 100);
        $top = mt_rand(0, 90);
        $left = mt_rand(0, 90);
        $opacity = mt_rand(70, 100) / 100;

        $html .= "<div class='square' style='
            position:absolute;
            width:{$size}px;
            height:{$size}px;
            top:{$top}%;
            left:{$left}%;
            background:red;
            opacity:{$opacity};
        '></div>";
    }

    $html .= "</div>";
    return $html;
}

// Кількість квадратів (demo)
$n = 15;

// Генеруємо
$squares = generateRandomSquares($n);

$content = $squares . '
    <div class="circles-func">generateRandomSquares(' . $n . ')</div>
    <div class="circles-counter">🟥 Квадратів: ' . $n . '</div>
    <p class="circles-info">Оновіть сторінку для нової композиції 🔄</p>';

renderDemoLayout($content, 'Завдання 7.2', 'task7-circles-body');
