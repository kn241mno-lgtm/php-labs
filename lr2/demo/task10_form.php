<?php
/**
 * Завдання 10: Реєстраційна форма
 *
 * Демонстрація: форма з POST, збереження в сесію, автозаповнення,
 * вибір мови через GET + cookie, завантаження фото
 */
session_start();
require_once __DIR__ . '/layout.php';

// --- Мова ---
$languages = [
    'uk' => 'Українська',
    'en' => 'English',
    'de' => 'Deutsch',
];

// GET -> cookie -> default
if (isset($_GET['lang']) && isset($languages[$_GET['lang']])) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang, time() + 6 * 30 * 24 * 3600, '/'); // 6 місяців
} elseif (isset($_COOKIE['lang']) && isset($languages[$_COOKIE['lang']])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'uk';
}

// --- Міста ---
$cities = [
    'Київ', 'Харків', 'Одеса', 'Дніпро', 'Запоріжжя',
    'Львів', 'Вінниця', 'Полтава', 'Житомир', 'Черкаси',
];

// --- Хобі ---
$hobbies = [
    'sport' => 'Спорт',
    'music' => 'Музика',
    'reading' => 'Читання',
    'gaming' => 'Ігри',
    'cooking' => 'Кулінарія',
    'travel' => 'Подорожі',
];

// --- Автозаповнення з сесії ---
$sessionData = $_SESSION['reg_data'] ?? [];

// --- Обробка форми ---
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $city = $_POST['city'] ?? '';
    $selectedHobbies = $_POST['hobbies'] ?? [];
    $about = trim($_POST['about'] ?? '');

    // Валідація
    if ($login === '') {
        $errors[] = 'Логін не може бути порожнім';
    }
    if (mb_strlen($password) < 4) {
        $errors[] = 'Пароль повинен бути не менше 4 символів';
    }
    if ($password !== $password2) {
        $errors[] = 'Паролі не збігаються';
    }
    if (!in_array($gender, ['male', 'female'])) {
        $errors[] = 'Оберіть стать';
    }
    if ($city === '') {
        $errors[] = 'Оберіть місто';
    }

    // Обробка фото
    $photoPath = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['photo']['type'], $allowedTypes)) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('photo_') . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $destination = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $photoPath = 'uploads/' . $newName;
            }
        } else {
            $errors[] = 'Дозволені формати фото: JPG, PNG, GIF, WEBP';
        }
    }

    // Зберігаємо в сесію (навіть якщо є помилки, для автозаповнення)
    $regData = [
        'login' => $login,
        'gender' => $gender,
        'city' => $city,
        'hobbies' => $selectedHobbies,
        'about' => $about,
        'photo' => $photoPath ?: ($sessionData['photo'] ?? ''),
    ];
    $_SESSION['reg_data'] = $regData;

    if (empty($errors)) {
        // Перенаправляємо на сторінку результатів
        header('Location: task10_result.php');
        exit;
    }
}

// Для автозаповнення
$formData = [
    'login' => $_POST['login'] ?? $sessionData['login'] ?? '',
    'gender' => $_POST['gender'] ?? $sessionData['gender'] ?? '',
    'city' => $_POST['city'] ?? $sessionData['city'] ?? '',
    'hobbies' => $_POST['hobbies'] ?? $sessionData['hobbies'] ?? [],
    'about' => $_POST['about'] ?? $sessionData['about'] ?? '',
];

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Реєстраційна форма</h2>

    <!-- Вибір мови -->
    <div class="lang-selector">
        <span style="font-size: 14px; color: var(--color-text-muted); margin-right: 8px;">Мова:</span>
        <?php foreach ($languages as $code => $name): ?>
        <a href="?lang=<?= $code ?>" class="<?= $lang === $code ? 'active' : '' ?>">
            <?= htmlspecialchars($name) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="lang-notice">Вибрана мова: <?= htmlspecialchars($languages[$lang]) ?></div>

    <?php if (!empty($errors)): ?>
    <div class="demo-result demo-result-error" style="margin-bottom: 20px;">
        <h3>Помилки</h3>
        <ul style="margin: 0; padding-left: 20px; text-align: left;">
            <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="demo-form" style="text-align: left;">
        <!-- Логін -->
        <div class="form-group">
            <label for="login">Логін</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($formData['login']) ?>" placeholder="Ваш логін" required>
        </div>

        <!-- Пароль -->
        <div class="form-group">
            <div class="form-row">
                <div>
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Мін. 4 символи" required>
                </div>
                <div>
                    <label for="password2">Повторіть пароль</label>
                    <input type="password" id="password2" name="password2" placeholder="Ще раз" required>
                </div>
            </div>
        </div>

        <!-- Стать -->
        <div class="form-group">
            <label>Стать</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="male" <?= $formData['gender'] === 'male' ? 'checked' : '' ?>>
                    Чоловіча
                </label>
                <label>
                    <input type="radio" name="gender" value="female" <?= $formData['gender'] === 'female' ? 'checked' : '' ?>>
                    Жіноча
                </label>
            </div>
        </div>

        <!-- Місто -->
        <div class="form-group">
            <label for="city">Місто</label>
            <select id="city" name="city" required>
                <option value="">-- Оберіть місто --</option>
                <?php foreach ($cities as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $formData['city'] === $c ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Хобі -->
        <div class="form-group">
            <label>Хобі</label>
            <div class="checkbox-group">
                <?php foreach ($hobbies as $key => $label): ?>
                <label>
                    <input type="checkbox" name="hobbies[]" value="<?= $key ?>" <?= in_array($key, $formData['hobbies']) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Про себе -->
        <div class="form-group">
            <label for="about">Про себе</label>
            <textarea id="about" name="about" rows="3" placeholder="Розкажіть про себе..."><?= htmlspecialchars($formData['about']) ?></textarea>
        </div>

        <!-- Фотографія -->
        <div class="form-group">
            <label for="photo">Фотографія</label>
            <input type="file" id="photo" name="photo" accept="image/*">
            <?php if (!empty($sessionData['photo']) && file_exists(__DIR__ . '/' . $sessionData['photo'])): ?>
            <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">
                Поточне фото збережено в сесії
            </p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit" style="align-self: flex-start;">Зареєструватися</button>
    </form>
</div>
<?php
$content = ob_get_clean();
renderDemoLayout($content, 'Форма: Реєстрація');
