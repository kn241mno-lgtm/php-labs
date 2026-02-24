<?php
/**
 * Завдання 6.2: 18 синіх кіл на білому тлі (по 5 у рядку)
 */
require_once __DIR__ . '/layout.php';

function generateCircles(int $n): string
{
    $html = "<div class='shapes-container shapes-container--white' style='
        display: grid;
        grid-template-columns: repeat(5, 60px);
        gap: 20px;
        justify-content: center;
        padding: 40px 20px;
    '>";

    $circleSize = 60;
    
    for ($i = 0; $i < $n; $i++) {
        $opacity = mt_rand(60, 100) / 100;

        $html .= "<div style='
            width:{$circleSize}px;
            height:{$circleSize}px;
            border-radius:50%;
            background-color:#3b82f6;
            opacity:{$opacity};
        '></div>";
    }

    $html .= "</div>";
    return $html;
}

$n = 18;
$circles = generateCircles($n);

$content = $circles . '
    <div class="circles-func">generateCircles(' . $n . ')</div>
    <div class="circles-counter">● Кіл: ' . $n . '</div>
    <p class="circles-info">Оновіть сторінку для нової композиції 🔄</p>';

renderVariantLayout($content, 'Завдання 6.2', 'task7-circles-body');
