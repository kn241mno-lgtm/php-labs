<?php
/**
 * Завдання 6.1: Шахова таблиця 11x5 комірок (чергування чорних і білих)
 */
require_once __DIR__ . '/layout.php';

function generateChessTable(int $rows, int $cols, string $color1, string $color2): string
{
     $html = "<table class='chessboard'>";
    for ($i = 0; $i < $rows; $i++) {
        $html .= "<tr>";
        for ($j = 0; $j < $cols; $j++) {
            $bgColor = (($i + $j) % 2 === 0) ? $color1 : $color2;
            $html .= "<td style='background-color:{$bgColor};'></td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

$rows = 11;
$cols = 5;
$color1 = '#000000';
$color2 = '#ffffff';

$table = generateChessTable($rows, $cols, $color1, $color2);

$content = '
    <h1>♟️ Шахова таблиця ' . $rows . 'x' . $cols . '</h1>
    <div class="params">generateChessTable(' . $rows . ', ' . $cols . ')</div>
    ' . $table;

renderVariantLayout($content, 'Завдання 6.1', 'task7-table-body');
