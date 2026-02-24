<?php
/**
 * Завдання 11: Калькулятор — результати обчислень
 *
 * Варіант 30: X = 8, Y = 4
 * sin(8)=0.9894, cos(8)=-0.1455, tg(8)=-6.7997, 8^4=4096, 8!=40320
 */
require_once __DIR__ . '/layout.php';

// --- Функції (Function/func.php) ---

function my_sin(float $x): float
{
    return sin($x);
}

function my_cos(float $x): float
{
    return cos($x);
}

function my_tan(float $x): float
{
    return tan($x);
}

function my_tg(float $x): string|float
{
    $cosX = cos($x);
    if (abs($cosX) < 1e-10) {
        return 'Не визначено (cos(x) = 0)';
    }
    return sin($x) / $cosX;
}

function my_pow(float $x, float $y): float
{
    return pow($x, $y);
}

function my_factorial(int $n): string|int
{
    if ($n < 0) {
        return 'Не визначено (x < 0)';
    }
    if ($n > 20) {
        return 'Занадто велике число (x > 20)';
    }
    if ($n <= 1) {
        return 1;
    }
    return $n * my_factorial($n - 1);
}

// --- Обробка ---
$x = isset($_POST['x']) ? (float)$_POST['x'] : null;
$y = isset($_POST['y']) ? (float)$_POST['y'] : null;

if ($x === null || $y === null) {
    header('Location: task11_calc.php');
    exit;
}

// Обчислення
$results = [
    ['func' => 'sin(x)', 'expression' => "sin($x)", 'value' => my_sin($x)],
    ['func' => 'cos(x)', 'expression' => "cos($x)", 'value' => my_cos($x)],
    ['func' => 'tg(x)', 'expression' => "tan($x)", 'value' => my_tan($x)],
    ['func' => 'my_tg(x)', 'expression' => "sin($x)/cos($x)", 'value' => my_tg($x)],
    ['func' => 'x^y', 'expression' => "{$x}^{$y}", 'value' => my_pow($x, $y)],
    ['func' => 'x!', 'expression' => (int)$x . '!', 'value' => my_factorial((int)$x)],
];

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Результати обчислень</h2>

    <div class="demo-grid-2">
        <div class="demo-result demo-result-info">
            <h3>X</h3>
            <div class="demo-result-value"><?= htmlspecialchars((string)$x) ?></div>
        </div>
        <div class="demo-result demo-result-info">
            <h3>Y</h3>
            <div class="demo-result-value"><?= htmlspecialchars((string)$y) ?></div>
        </div>
    </div>

    <table class="calc-table mt-15">
        <thead>
            <tr>
                <th>Функція</th>
                <th>Вираз</th>
                <th>Результат</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['func']) ?></td>
                <td><?= htmlspecialchars($row['expression']) ?></td>
                <td>
                    <?php
                    if (is_string($row['value'])) {
                        echo '<span class="demo-tag demo-tag-error">' . htmlspecialchars($row['value']) . '</span>';
                    } else {
                        echo round($row['value'], 10);
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="flex-buttons mt-15">
        <a href="task11_calc.php?x=<?= urlencode((string)$x) ?>&y=<?= urlencode((string)$y) ?>" class="btn-secondary">Назад до калькулятора</a>
        <a href="task11_calc.php" class="btn-secondary">Нові значення</a>
    </div>

    <div class="demo-code">// Function/func.php
require_once 'Function/func.php';

$x = <?= $x ?>;
$y = <?= $y ?>;

my_sin($x)      = <?= is_string($results[0]['value']) ? $results[0]['value'] : round($results[0]['value'], 10) . "\n" ?>
my_cos($x)      = <?= is_string($results[1]['value']) ? $results[1]['value'] : round($results[1]['value'], 10) . "\n" ?>
my_tan($x)      = <?= is_string($results[2]['value']) ? $results[2]['value'] : round($results[2]['value'], 10) . "\n" ?>
my_tg($x)       = <?= is_string($results[3]['value']) ? $results[3]['value'] : round($results[3]['value'], 10) . "\n" ?>
my_pow($x, $y)  = <?= round($results[4]['value'], 10) . "\n" ?>
my_factorial(<?= (int)$x ?>) = <?= $results[5]['value'] ?></div>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 11');
