<?php
/**
 * Завдання 5: Генератор паролів
 *
 * Довжина: 8
 * Вимоги:
 *  - 1 велика літера
 *  - 1 мала літера
 *  - 1 цифра
 *  - 1 спецсимвол (!@#$%^&*()-_=+)
 * 
 * Генерується 3 паролі і обирається найсильніший
 */

require_once __DIR__ . '/layout.php';

/**
 * Генерація одного пароля
 */
function generatePassword(int $length = 8): string
{
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $special = '!@#$%^&*()-_=+';

    $all = $upper . $lower . $digits . $special;

    $password = '';

    // гарантуємо виконання вимог
    $password .= $upper[random_int(0, strlen($upper)-1)];
    $password .= $lower[random_int(0, strlen($lower)-1)];
    $password .= $digits[random_int(0, strlen($digits)-1)];
    $password .= $special[random_int(0, strlen($special)-1)];

    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all)-1)];
    }

    return str_shuffle($password);
}

/**
 * Перевірка складності пароля
 */
function checkPasswordStrength(string $password): array
{
    $length = strlen($password);

    $checks = [
        'length' => ['label'=>'Довжина ≥ 8','passed'=>$length >= 8],
        'upper' => ['label'=>'Велика літера','passed'=>preg_match('/[A-Z]/',$password)],
        'lower' => ['label'=>'Мала літера','passed'=>preg_match('/[a-z]/',$password)],
        'digit' => ['label'=>'Цифра','passed'=>preg_match('/[0-9]/',$password)],
        'special' => ['label'=>'Спецсимвол','passed'=>preg_match('/[^a-zA-Z0-9]/',$password)]
    ];

    $score = 0;

    foreach ($checks as $c) {
        if ($c['passed']) $score++;
    }

    return [
        'score'=>$score,
        'checks'=>$checks
    ];
}

/**
 * Генерація 3 паролів і вибір найсильнішого
 */
function generateBestPassword(): array
{
    $passwords = [];
    $best = null;
    $bestScore = -1;

    for ($i=0; $i<3; $i++) {

        $pass = generatePassword(8);
        $strength = checkPasswordStrength($pass);

        $passwords[] = [
            'password'=>$pass,
            'score'=>$strength['score']
        ];

        if ($strength['score'] > $bestScore) {
            $bestScore = $strength['score'];
            $best = $pass;
        }
    }

    return [
        'passwords'=>$passwords,
        'best'=>$best,
        'bestScore'=>$bestScore
    ];
}

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = generateBestPassword();
}

ob_start();
?>

<div class="demo-card demo-card-wide">

<h2>Генератор паролів</h2>

<form method="post">
<button type="submit" class="btn-submit">Згенерувати 3 паролі</button>
</form>

<?php if ($result): ?>

<h3>Згенеровані паролі</h3>

<table class="demo-table">

<tr>
<th>№</th>
<th>Пароль</th>
<th>Бали складності</th>
</tr>

<?php foreach ($result['passwords'] as $i=>$p): ?>

<tr>
<td><?= $i+1 ?></td>
<td class="demo-mono"><?= htmlspecialchars($p['password']) ?></td>
<td><?= $p['score'] ?>/5</td>
</tr>

<?php endforeach; ?>

</table>

<div class="demo-result mt-15">

<h3>Найсильніший пароль</h3>

<div class="demo-result-value demo-mono">
<?= htmlspecialchars($result['best']) ?>
</div>

<p>Складність: <b><?= $result['bestScore'] ?>/5</b></p>

</div>

<?php endif; ?>

</div>

<?php

$content = ob_get_clean();

renderVariantLayout($content,'Завдання 5');