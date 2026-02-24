<?php
/**
 * Завдання 10: Результат реєстрації
 *
 * Варіант 30: відображає дані збережені в сесії
 */
session_start();
require_once __DIR__ . '/layout.php';

$hobbiesMap = [
    'sport' => 'Спорт',
    'music' => 'Музика',
    'reading' => 'Читання',
    'gaming' => 'Ігри',
    'cooking' => 'Кулінарія',
    'travel' => 'Подорожі',
];

$data = $_SESSION['reg_data'] ?? null;

// Мова
$languages = [
    'uk' => 'Українська',
    'en' => 'English',
    'de' => 'Deutsch',
];
$lang = $_COOKIE['lang'] ?? 'uk';
if (!isset($languages[$lang])) {
    $lang = 'uk';
}

ob_start();
?>
<div class="demo-card demo-card-wide">
    <h2>Результат реєстрації</h2>

    <div class="lang-notice">Вибрана мова: <?= htmlspecialchars($languages[$lang]) ?></div>

    <?php if ($data): ?>
    <div class="demo-result">
        <h3>Реєстрацію завершено</h3>
        <div class="demo-result-value">Дані збережено в сесії</div>
    </div>

    <div class="result-data mt-15">
        <div class="result-data-row">
            <span class="result-data-label">Логін</span>
            <span class="result-data-value"><?= htmlspecialchars($data['login'] ?? '') ?></span>
        </div>
        <div class="result-data-row">
            <span class="result-data-label">Стать</span>
            <span class="result-data-value">
                <?php
                $genderMap = ['male' => 'Чоловіча', 'female' => 'Жіноча'];
                echo htmlspecialchars($genderMap[$data['gender'] ?? ''] ?? 'Не вказано');
                ?>
            </span>
        </div>
        <div class="result-data-row">
            <span class="result-data-label">Місто</span>
            <span class="result-data-value"><?= htmlspecialchars($data['city'] ?? '') ?></span>
        </div>
        <div class="result-data-row">
            <span class="result-data-label">Хобі</span>
            <span class="result-data-value">
                <?php
                $selectedHobbies = $data['hobbies'] ?? [];
                if (!empty($selectedHobbies)) {
                    $labels = array_reduce($selectedHobbies, function (array $acc, string $key) use ($hobbiesMap) {
                        if (isset($hobbiesMap[$key])) {
                            $acc[] = $hobbiesMap[$key];
                        }
                        return $acc;
                    }, []);
                    foreach ($labels as $label) {
                        echo '<span class="demo-tag demo-tag-primary">' . htmlspecialchars($label) . '</span> ';
                    }
                } else {
                    echo 'Не вказано';
                }
                ?>
            </span>
        </div>
        <div class="result-data-row">
            <span class="result-data-label">Про себе</span>
            <span class="result-data-value"><?= nl2br(htmlspecialchars($data['about'] ?? 'Не вказано')) ?></span>
        </div>

        <?php if (!empty($data['photo']) && file_exists(__DIR__ . '/' . $data['photo'])): ?>
        <div class="result-data-row">
            <span class="result-data-label">Фотографія</span>
            <span class="result-data-value">
                <img src="<?= htmlspecialchars($data['photo']) ?>" alt="Фото користувача" class="photo-preview">
            </span>
        </div>
        <?php endif; ?>
    </div>

    <div class="flex-buttons mt-15">
        <a href="task10_form.php" class="btn-secondary">Повернутися до форми</a>
    </div>

    <?php else: ?>
    <div class="demo-result demo-result-error">
        <h3>Помилка</h3>
        <div class="demo-result-value">Дані реєстрації не знайдено. Заповніть форму спочатку.</div>
    </div>
    <div class="flex-buttons mt-15">
        <a href="task10_form.php" class="btn-secondary">Перейти до форми</a>
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
renderVariantLayout($content, 'Завдання 10');
