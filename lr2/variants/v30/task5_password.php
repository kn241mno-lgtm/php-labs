<?php
/**
 * Завдання 5: Генератор паролів (без підрядка логіну)
 *
 * Варіант 30 (група C, Sub3): пароль без підрядка логіну (>= 3 символи)
 * Довжина: 16
 */
require_once __DIR__ . '/layout.php';

function generatePassword(int $length = 16): string
{
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $special = '!@#$%^&*()-_=+';
    $all = $upper . $lower . $digits . $special;

    $password = '';
    $password .= $upper[random_int(0, strlen($upper) - 1)];
    $password .= $lower[random_int(0, strlen($lower) - 1)];
    $password .= $digits[random_int(0, strlen($digits) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];

    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }

    return str_shuffle($password);
}

/**
 * Генерує пароль, що не містить підрядка логіну (>= 3 символи)
 */
function generatePasswordWithoutLogin(int $length, string $login, int $maxAttempts = 100): string
{
    for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        $password = generatePassword($length);
        if (!containsLoginSubstring($password, $login)) {
            return $password;
        }
    }
    return $password; // fallback
}

/**
 * Перевіряє чи пароль містить підрядок логіну (>= 3 символи)
 */
function containsLoginSubstring(string $password, string $login, int $minLength = 3): bool
{
    $loginLower = function_exists('mb_strtolower') ? mb_strtolower($login) : strtolower($login);
    $passwordLower = function_exists('mb_strtolower') ? mb_strtolower($password) : strtolower($password);
    $loginLen = function_exists('mb_strlen') ? mb_strlen($loginLower) : strlen($loginLower);

    for ($len = $minLength; $len <= $loginLen; $len++) {
        for ($start = 0; $start <= $loginLen - $len; $start++) {
            $sub = function_exists('mb_substr') ? mb_substr($loginLower, $start, $len) : substr($loginLower, $start, $len);
            if (strpos($passwordLower, $sub) !== false) {
                return true;
            }
        }
    }
    return false;
}

function checkPasswordStrength(string $password): array
{
    $checks = [
        'length' => ['label' => 'Довжина >= 8 символів', 'passed' => (function_exists('mb_strlen') ? mb_strlen($password) : strlen($password)) >= 8],
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

// Обробка (варіант 30)
$action = $_POST['action'] ?? '';
$genLength = (int)($_POST['gen_length'] ?? 16);
$login = $_POST['login'] ?? 'teacher_math';
$checkPassword = $_POST['check_password'] ?? '';
$generated = '';
$strengthResult = null;
$loginCheck = null;

if ($genLength < 4) $genLength = 4;
if ($genLength > 128) $genLength = 128;

if ($action === 'generate') {
    $generated = generatePasswordWithoutLogin($genLength, $login);
    $strengthResult = checkPasswordStrength($generated);
    $loginCheck = containsLoginSubstring($generated, $login);
} elseif ($action === 'check' && $checkPassword !== '') {
    $strengthResult = checkPasswordStrength($checkPassword);
    $loginCheck = containsLoginSubstring($checkPassword, $login);
}

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Генератор паролів (без логіну)</h2>
    <p class="demo-subtitle">Пароль не повинен містити підрядок логіну (>= 3 символи)</p>

    <div class="demo-grid-2">
        <!-- Генератор -->
        <div class="demo-panel">
            <h3 class="demo-panel-title-primary">Генератор</h3>
            <form method="post" class="demo-form">
                <input type="hidden" name="action" value="generate">
                <div>
                    <label for="login">Логін</label>
                    <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" placeholder="Ваш логін">
                </div>
                <div>
                    <label for="gen_length">Довжина пароля</label>
                    <input type="number" id="gen_length" name="gen_length" value="<?= $genLength ?>" min="4" max="128">
                </div>
                <button type="submit" class="btn-submit">Згенерувати</button>
            </form>

            <?php if ($generated): ?>
            <div class="demo-result mt-15">
                <h3>Згенерований пароль</h3>
                <div class="demo-result-value demo-mono"><?= htmlspecialchars($generated) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Перевірка -->
        <div class="demo-panel">
            <h3 class="demo-panel-title-success">Перевірка міцності</h3>
            <form method="post" class="demo-form">
                <input type="hidden" name="action" value="check">
                <input type="hidden" name="login" value="<?= htmlspecialchars($login) ?>">
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

        <?php if ($loginCheck !== null): ?>
        <p>Містить підрядок логіну "<?= htmlspecialchars($login) ?>":
            <?php if ($loginCheck): ?>
            <span class="demo-tag demo-tag-error">Так (небезпечно!)</span>
            <?php else: ?>
            <span class="demo-tag demo-tag-success">Ні</span>
            <?php endif; ?>
        </p>
        <?php endif; ?>

        <div class="strength-meter">
            <div class="strength-meter-fill strength-<?= $strengthResult['strength'] ?>"></div>
        </div>

        <table class="demo-table mt-15">
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

        <div class="demo-code">generatePasswordWithoutLogin(<?= $genLength ?>, "<?= htmlspecialchars($login) ?>")
// containsLoginSubstring(password, login, minLength: 3)
// score: <?= $strengthResult['score'] ?>/<?= $strengthResult['total'] ?>, strength: "<?= $strengthResult['strength'] ?>"</div>
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 5');
