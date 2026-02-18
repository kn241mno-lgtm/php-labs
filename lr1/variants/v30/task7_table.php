<?php
/**
 * Завдання 6.1: Смугаста таблиця 11x8
 */
require_once __DIR__ . '/layout.php';

function generateStripedTable(int $rows, int $cols, string $color1, string $color2): string
{
    $html = "<table class='chessboard'>";
    for ($i = 0; $i < $rows; $i++) {
        $bgColor = ($i % 2 === 0) ? $color1 : $color2;
        $html .= "<tr>";
        for ($j = 0; $j < $cols; $j++) {
            $html .= "<td style='background-color:{$bgColor};'></td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

$rows = 11;
$cols = 8;
$color1 = '#6366f1';
$color2 = '#a5b4fc';

$table = generateStripedTable($rows, $cols, $color1, $color2);

$content = '
    <h1>♟️ Смугаста таблиця ' . $rows . 'x' . $cols . '</h1>
    <div class="params">generateStripedTable(' . $rows . ', ' . $cols . ')</div>
    ' . $table;

renderVariantLayout($content, 'Завдання 6.1', 'task7-table-body');
