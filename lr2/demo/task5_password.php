<?php
/**
 * Завдання 5: Генератор + перевірка паролів
 *
 * Демонстрація: генерація випадкового пароля, перевірка міцності
 */
require_once __DIR__ . '/layout.php';

/**
 * Генерує випадковий пароль заданої довжини
 */
function generatePassword(int $length = 12): string
{
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $special = '!@#$%^&*()-_=+';
    $all = $upper . $lower . $digits . $special;

    // Гарантуємо наявність хоча б одного символу кожного типу
    $password = '';
    $password .= $upper[random_int(0, strlen($upper) - 1)];
    $password .= $lower[random_int(0, strlen($lower) - 1)];
    $password .= $digits[random_int(0, strlen($digits) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];

    // Заповнюємо решту довжини
    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }

    // Перемішуємо символи
    return str_shuffle($password);
}

/**
 * Перевіряє міцність пароля
 *
 * @return array ['strength' => string, 'score' => int, 'checks' => array]
 */
function checkPasswordStrength(string $password): array
{
    $checks = [
        'length' => ['label' => 'Довжина >= 8 символів', 'passed' => mb_strlen($password) >= 8],
        'upper' => ['label' => 'Містить велику літеру', 'passed' => (bool)preg_match('/[A-Z]/', $password)],
        'lower' => ['label' => 'Містить малу літеру', 'passed' => (bool)preg_match('/[a-z]/', $password)],
        'digit' => ['label' => 'Містить цифру', 'passed' => (bool)preg_match('/[0-9]/', $password)],
        'special' => ['label' => 'Містить спецсимвол', 'passed' => (bool)preg_match('/[^a-zA-Z0-9]/', $password)],
    ];

    $score = array_reduce($checks, fn(int $acc, array $check) => $acc + ($check['passed'] ? 1 : 0), 0);

    $strength = match (true) {
        $score <= 1 => 'weak',
        $score <= 2 => 'fair',
        $score <= 3 => 'good',
        default => 'strong',
    };

    $strengthLabels = [
        'weak' => 'Слабкий',
        'fair' => 'Задовільний',
        'good' => 'Добрий',
        'strong' => 'Надійний',
    ];

    return [
        'strength' => $strength,
        'strengthLabel' => $strengthLabels[$strength],
        'score' => $score,
        'total' => count($checks),
        'checks' => $checks,
    ];
}

// Обробка
$action = $_POST['action'] ?? '';
$genLength = (int)($_POST['gen_length'] ?? 12);
$checkPassword = $_POST['check_password'] ?? '';
$generated = '';
$strengthResult = null;

if ($genLength < 4) $genLength = 4;
if ($genLength > 128) $genLength = 128;

if ($action === 'generate') {
    $generated = generatePassword($genLength);
    $strengthResult = checkPasswordStrength($generated);
} elseif ($action === 'check' && $checkPassword !== '') {
    $strengthResult = checkPasswordStrength($checkPassword);
}

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Генератор та перевірка паролів</h2>
    <p style="color: var(--color-text-muted); margin-top: 0;">Генерація випадкових паролів та оцінка їх міцності</p>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Генератор -->
        <div style="background: var(--color-bg); padding: 20px; border-radius: var(--radius-md);">
            <h3 style="margin: 0 0 16px; color: var(--color-primary);">Генератор</h3>
            <form method="post" class="demo-form">
                <input type="hidden" name="action" value="generate">
                <div>
                    <label for="gen_length">Довжина пароля</label>
                    <input type="number" id="gen_length" name="gen_length" value="<?= $genLength ?>" min="4" max="128">
                </div>
                <button type="submit" class="btn-submit">Згенерувати</button>
            </form>

            <?php if ($generated): ?>
            <div class="demo-result" style="margin-top: 16px;">
                <h3>Згенерований пароль</h3>
                <div class="demo-result-value" style="font-family: var(--font-mono); letter-spacing: 1px;"><?= htmlspecialchars($generated) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Перевірка -->
        <div style="background: var(--color-bg); padding: 20px; border-radius: var(--radius-md);">
            <h3 style="margin: 0 0 16px; color: var(--color-success);">Перевірка міцності</h3>
            <form method="post" class="demo-form">
                <input type="hidden" name="action" value="check">
                <div>
                    <label for="check_password">Пароль для перевірки</label>
                    <input type="text" id="check_password" name="check_password" value="<?= htmlspecialchars($checkPassword) ?>" placeholder="Введіть пароль">
                </div>
                <button type="submit" class="btn-submit btn-success">Перевірити</button>
            </form>
        </div>
    </div>

    <?php if ($strengthResult): ?>
    <div class="demo-section">
        <h3>Результат перевірки: <span class="demo-tag demo-tag-<?= match($strengthResult['strength']) {
            'weak' => 'error',
            'fair' => 'warning',
            'good' => 'primary',
            'strong' => 'success',
        } ?>"><?= htmlspecialchars($strengthResult['strengthLabel']) ?></span></h3>

        <div class="strength-meter">
            <div class="strength-meter-fill strength-<?= $strengthResult['strength'] ?>"></div>
        </div>

        <table class="demo-table" style="margin-top: 16px;">
            <thead>
                <tr>
                    <th>Критерій</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($strengthResult['checks'] as $check): ?>
                <tr>
                    <td><?= htmlspecialchars($check['label']) ?></td>
                    <td>
                        <?php if ($check['passed']): ?>
                        <span class="demo-tag demo-tag-success">Так</span>
                        <?php else: ?>
                        <span class="demo-tag demo-tag-error">Ні</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="demo-code">checkPasswordStrength("<?= htmlspecialchars($generated ?: $checkPassword) ?>")
// score: <?= $strengthResult['score'] ?>/<?= $strengthResult['total'] ?>, strength: "<?= $strengthResult['strength'] ?>"</div>
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Рядки: Паролі');
