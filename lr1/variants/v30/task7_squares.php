<?php
/**
 * Завдання 6.2: 20 зелених трикутників на сірому тлі (розмір зростає)
 */
require_once __DIR__ . '/layout.php';

function generateGrowingTriangles(int $n): string
{
    $html = "<div style='position:relative;width:100vw;height:100vh;background:#374151;overflow:hidden;'>";

    for ($i = 0; $i < $n; $i++) {
        $size = 20 + $i * 5;
        $top = mt_rand(5, 85);
        $left = mt_rand(5, 85);
        $opacity = mt_rand(70, 100) / 100;

        $halfSize = (int)($size / 2);
        $html .= "<div style='
            position:absolute;
            top:{$top}%;
            left:{$left}%;
            width:0;
            height:0;
            border-left:{$halfSize}px solid transparent;
            border-right:{$halfSize}px solid transparent;
            border-bottom:{$size}px solid #10b981;
            opacity:{$opacity};
        '></div>";
    }

    $html .= "</div>";
    return $html;
}

$n = 20;
$triangles = generateGrowingTriangles($n);

$content = $triangles . '
    <div class="circles-func">generateGrowingTriangles(' . $n . ')</div>
    <div class="circles-counter">🔺 Трикутників: ' . $n . '</div>
    <p class="circles-info">Оновіть сторінку для нової композиції 🔄</p>';

renderVariantLayout($content, 'Завдання 6.2', 'task7-circles-body');
