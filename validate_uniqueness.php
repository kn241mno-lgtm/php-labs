<?php
/**
 * Variant Uniqueness Validator
 *
 * Validates that all vN.md files in a lab have unique data
 * and correct math (where applicable).
 *
 * Usage: php validate_uniqueness.php lr1
 *        php validate_uniqueness.php lr1 --verbose
 */

$lab = $argv[1] ?? null;
$verbose = in_array('--verbose', $argv ?? []);

if (!$lab) {
    echo "\033[1mUsage:\033[0m php validate_uniqueness.php <lab> [--verbose]\n";
    echo "  Example: php validate_uniqueness.php lr1\n";
    exit(1);
}

$labDir = __DIR__ . "/{$lab}/variants";
if (!is_dir($labDir)) {
    echo "\033[31mError:\033[0m Directory not found: {$labDir}\n";
    exit(1);
}

// Find all vN.md files
$files = glob("{$labDir}/v*.md");
usort($files, function ($a, $b) {
    preg_match('/v(\d+)\.md$/', $a, $ma);
    preg_match('/v(\d+)\.md$/', $b, $mb);
    return (int)($ma[1] ?? 0) - (int)($mb[1] ?? 0);
});

if (empty($files)) {
    echo "\033[31mError:\033[0m No variant files found in {$labDir}\n";
    exit(1);
}

echo "\033[1m=== Variant Uniqueness Validator ===\033[0m\n";
echo "Lab: \033[1m{$lab}\033[0m | Files: \033[1m" . count($files) . "\033[0m\n\n";

// Parse variants based on lab
$variants = [];
$errors = [];
$warnings = [];

foreach ($files as $file) {
    $basename = basename($file, '.md');
    $content = file_get_contents($file);
    $data = parseVariant($content, $lab, $basename);
    $data['file'] = $basename;
    $variants[$basename] = $data;
}

// Check uniqueness
checkUniqueness($variants, $errors, $warnings, $verbose);

// Verify math (LR1)
if ($lab === 'lr1') {
    verifyMath($variants, $errors);
}

// Verify math (LR2)
if ($lab === 'lr2') {
    verifyMathLR2($variants, $errors);
}

// Report
echo "\033[1m--- Results ---\033[0m\n";

if (empty($errors) && empty($warnings)) {
    echo "\033[32m✓ All " . count($files) . " variants pass uniqueness and math checks\033[0m\n";
} else {
    foreach ($warnings as $w) {
        echo "\033[33m⚠ {$w}\033[0m\n";
    }
    foreach ($errors as $e) {
        echo "\033[31m✗ {$e}\033[0m\n";
    }
    echo "\n";
    $ec = count($errors);
    $wc = count($warnings);
    if ($ec > 0) {
        echo "\033[31m{$ec} error(s)\033[0m";
    }
    if ($wc > 0) {
        echo ($ec > 0 ? ", " : "") . "\033[33m{$wc} warning(s)\033[0m";
    }
    echo "\n";
}

exit(empty($errors) ? 0 : 1);

// ===== Functions =====

function getVariantGroup(string $basename): string
{
    preg_match('/v(\d+)/', $basename, $m);
    $num = (int)($m[1] ?? 0);
    if ($num <= 10) return 'A';
    if ($num <= 20) return 'B';
    return 'C';
}

function getVariantSubgroup(string $basename): int
{
    preg_match('/v(\d+)/', $basename, $m);
    $num = (int)($m[1] ?? 0);
    $inGroup = (($num - 1) % 10) + 1; // 1-10 within group
    if ($inGroup <= 3) return 1;
    if ($inGroup <= 7) return 2;
    return 3;
}

function parseVariant(string $content, string $lab, string $basename): array
{
    $data = [];

    switch ($lab) {
        case 'lr1':
            $group = getVariantGroup($basename);
            $data = parseLR1($content, $group);
            $data['group'] = $group;
            $data['subgroup'] = getVariantSubgroup($basename);
            break;
        case 'lr2':
            $group = getVariantGroup($basename);
            $data = parseLR2($content, $group, $basename);
            $data['group'] = $group;
            $data['subgroup'] = getVariantSubgroup($basename);
            break;
        default:
            // Generic: extract all numbers and key text blocks
            $data = parseGeneric($content);
            break;
    }

    return $data;
}

function parseLR1(string $content, string $group): array
{
    $data = [];

    // Common fields across all groups

    // Month: "Місяць для перевірки: 3"
    if (preg_match('/Місяць для перевірки:\s*(\d+)/u', $content, $m)) {
        $data['month'] = (int)$m[1];
    }

    // Letter: "Символ для перевірки: 'а'"
    if (preg_match("/Символ для перевірки:\s*'(.+?)'/u", $content, $m)) {
        $data['letter'] = $m[1];
    }

    // 3-digit number: "Число: 427"
    if (preg_match('/Число:\s*(\d{3})/u', $content, $m)) {
        $data['number'] = (int)$m[1];
    }

    // Digit sum: "Сума цифр: 13"
    if (preg_match('/Сума цифр:\s*(\d+)/u', $content, $m)) {
        $data['digit_sum'] = (int)$m[1];
    }

    // Reversed number: "Зворотне число: 724"
    if (preg_match('/Зворотне число:\s*(\d+)/u', $content, $m)) {
        $data['reversed'] = (int)$m[1];
    }

    // Max number: "Найбільше число: 742" (Sub1)
    if (preg_match('/Найбільше число:\s*(\d+)/u', $content, $m)) {
        $data['max_number'] = (int)$m[1];
    }

    // Min number: "Найменше число: 123" or "Найменше число: 057 (як число: 57)" (Sub2)
    if (preg_match('/Найменше число:\s*(\d+)/u', $content, $m)) {
        $data['min_number'] = (int)$m[1];
    }

    // Palindrome: "Паліндром: так" or "Паліндром: ні" (Sub3)
    if (preg_match('/Паліндром:\s*(так|ні)/u', $content, $m)) {
        $data['palindrome'] = $m[1];
    }

    // Table dimensions: "Таблиця: 4 x 5" or "таблиця: 4 x 5"
    if (preg_match('/[Тт]аблиця:\s*(\d+)\s*x\s*(\d+)/u', $content, $m)) {
        $data['table'] = $m[1] . 'x' . $m[2];
    }

    // Poem (first line after ```)
    if (preg_match('/```\s*\n(.+?)\n/u', $content, $m)) {
        $data['poem_first_line'] = trim($m[1]);
    }

    // Group-specific parsing
    switch ($group) {
        case 'A':
            parseLR1GroupA($content, $data);
            break;
        case 'B':
            parseLR1GroupB($content, $data);
            break;
        case 'C':
            parseLR1GroupC($content, $data);
            break;
    }

    return $data;
}

function parseLR1GroupA(string $content, array &$data): void
{
    // Currency: UAH → USD (division)
    // "Сума: 1500 грн" + "Курс: 1 долар = 41.25 грн"
    if (preg_match('/Сума:\s*([\d]+)\s*грн/u', $content, $m)) {
        $data['amount'] = (int)$m[1];
    }
    if (preg_match('/Курс:\s*1 долар\s*=\s*([\d.]+)\s*грн/u', $content, $m)) {
        $data['rate'] = (float)$m[1];
    }
    if (preg_match('/обміняти на\s*([\d.]+)\s*долар/u', $content, $m)) {
        $data['conversion_result'] = (float)$m[1];
    }

    // Shapes: "Квадрати: 6 червоних квадратів"
    if (preg_match('/Квадрат[иів]*:\s*(\d+)/u', $content, $m)) {
        $data['shapes'] = (int)$m[1];
    }
}

function parseLR1GroupB(string $content, array &$data): void
{
    // Currency: USD → UAH (multiplication)
    // "Сума: 150 доларів" + "Курс: 1 долар = 39.20 грн"
    if (preg_match('/Сума:\s*([\d]+)\s*долар/u', $content, $m)) {
        $data['amount'] = (int)$m[1];
    }
    if (preg_match('/Курс:\s*1 долар\s*=\s*([\d.]+)\s*грн/u', $content, $m)) {
        $data['rate'] = (float)$m[1];
    }
    if (preg_match('/обміняти на\s*([\d.]+)\s*грн/u', $content, $m)) {
        $data['conversion_result'] = (float)$m[1];
    }

    // Shapes: "Кола: 9 синіх кіл"
    if (preg_match('/Кола?:\s*(\d+)/u', $content, $m)) {
        $data['shapes'] = (int)$m[1];
    }
}

function parseLR1GroupC(string $content, array &$data): void
{
    // Currency: UAH → EUR with commission
    // "Сума: 12500 грн" + "Курс: 1 євро = 45.30 грн" + "Комісія банку: 2%"
    if (preg_match('/Сума:\s*([\d]+)\s*грн/u', $content, $m)) {
        $data['amount'] = (int)$m[1];
    }
    if (preg_match('/Курс:\s*1 євро\s*=\s*([\d.]+)\s*грн/u', $content, $m)) {
        $data['rate'] = (float)$m[1];
    }
    if (preg_match('/Комісія банку:\s*([\d.]+)%/u', $content, $m)) {
        $data['commission'] = (float)$m[1];
    }
    // Before commission result
    if (preg_match('/=\s*([\d.]+)\s*євро,\s*після/u', $content, $m)) {
        $data['conversion_before'] = (float)$m[1];
    }
    // After commission result
    if (preg_match('/після комісії\s*[\d.]+%\s*—\s*([\d.]+)\s*євро/u', $content, $m)) {
        $data['conversion_after'] = (float)$m[1];
    }

    // Shapes: "Трикутники: 7 зелених трикутників"
    if (preg_match('/Трикутник[иів]*:\s*(\d+)/u', $content, $m)) {
        $data['shapes'] = (int)$m[1];
    }
}

function parseLR2(string $content, string $group, string $basename): array
{
    $data = [];
    $subgroup = getVariantSubgroup($basename);

    // Task 4: Date difference
    if (preg_match('/Дата 1:\s*"(\d{2}-\d{2}-\d{4})"/u', $content, $m)) {
        $data['date1'] = $m[1];
    }
    if (preg_match('/Дата 2:\s*"(\d{2}-\d{2}-\d{4})"/u', $content, $m)) {
        $data['date2'] = $m[1];
    }
    if (preg_match('/Очікуваний результат:\s*(\d+)\s*днів/u', $content, $m)) {
        $data['days'] = (int)$m[1];
    }

    // Sub2: weeks + remainder
    if (preg_match('/(\d+)\s*тижнів?\s*і\s*(\d+)\s*днів/u', $content, $m)) {
        $data['weeks'] = (int)$m[1];
        $data['remainder_days'] = (int)$m[2];
    }

    // Sub3: weekdays
    if (preg_match('/Дні тижня:\s*(.+)/u', $content, $m)) {
        $data['weekdays'] = trim($m[1]);
    }

    // Task 5: Password
    if (preg_match('/Довжина пароля:\s*(\d+)/u', $content, $m)) {
        $data['password_length'] = (int)$m[1];
    }

    // Sub2: generate 3 mode
    $data['password_mode'] = 'single';
    if (preg_match('/згенерувати 3 паролі/u', $content)) {
        $data['password_mode'] = 'best_of_3';
    }
    if (preg_match('/не повинен містити підрядок логіну/u', $content)) {
        $data['password_mode'] = 'no_login';
    }

    // Task 10: Login
    if (preg_match('/Логін для прикладу:\s*"([^"]+)"/u', $content, $m)) {
        $data['login'] = $m[1];
    }

    return $data;
}

function parseGeneric(string $content): array
{
    $data = [];

    // Extract all numbers
    preg_match_all('/\b\d+(?:\.\d+)?\b/', $content, $matches);
    $data['all_numbers'] = $matches[0] ?? [];

    // Extract text between ``` blocks
    preg_match_all('/```\s*\n(.+?)\n```/su', $content, $matches);
    $data['code_blocks'] = $matches[1] ?? [];

    return $data;
}

function checkUniqueness(array $variants, array &$errors, array &$warnings, bool $verbose): void
{
    // Collect all field values
    $fields = [];
    foreach ($variants as $name => $data) {
        foreach ($data as $key => $value) {
            if (in_array($key, ['file', 'group', 'all_numbers', 'code_blocks'])) {
                continue;
            }
            $fields[$key][$name] = $value;
        }
    }

    echo "\033[1mUniqueness check:\033[0m\n";

    foreach ($fields as $field => $values) {
        // Rebuild duplicates properly
        $dupsClean = [];
        foreach ($values as $variant => $value) {
            $key = (string)$value;
            $dupsClean[$key][] = $variant;
        }
        $dupsClean = array_filter($dupsClean, fn($v) => count($v) > 1);

        $uniqueCount = count(array_unique(array_map('strval', $values)));
        $totalCount = count($values);

        if (empty($dupsClean)) {
            echo "  \033[32m✓\033[0m {$field}: {$uniqueCount}/{$totalCount} unique\n";
            if ($verbose) {
                $sortedValues = $values;
                asort($sortedValues);
                foreach ($sortedValues as $v => $val) {
                    echo "    {$v}: {$val}\n";
                }
            }
        } else {
            // Fields that are allowed to repeat (derived or limited range)
            $warnFields = ['month', 'digit_sum', 'reversed', 'max_number', 'min_number', 'palindrome', 'shapes', 'commission', 'subgroup', 'password_mode', 'weeks', 'remainder_days', 'password_length', 'days', 'date1', 'date2'];
            if (in_array($field, $warnFields)) {
                $reason = match ($field) {
                    'month' => 'only 12 months',
                    'digit_sum', 'reversed', 'max_number', 'min_number' => 'derived from number',
                    'palindrome' => 'binary value (так/ні)',
                    'shapes' => 'limited range',
                    'commission' => 'limited range of reasonable values',
                    'subgroup' => 'only 3 subgroups',
                    'password_mode' => 'only 3 modes',
                    'weeks', 'remainder_days' => 'derived from days',
                    'password_length' => 'limited range (8-20)',
                    'days' => 'derived from dates',
                    'date1', 'date2' => 'dates can overlap across groups',
                };
                $warnings[] = "{$field}: {$uniqueCount}/{$totalCount} unique ({$reason})";
                echo "  \033[33m⚠\033[0m {$field}: {$uniqueCount}/{$totalCount} unique (OK — {$reason})\n";
            } else {
                $errors[] = "{$field}: duplicates found ({$uniqueCount}/{$totalCount} unique)";
                echo "  \033[31m✗\033[0m {$field}: {$uniqueCount}/{$totalCount} unique — DUPLICATES:\n";
                foreach ($dupsClean as $val => $vars) {
                    echo "    \033[31m'{$val}' in: " . implode(', ', $vars) . "\033[0m\n";
                }
            }
        }
    }
    echo "\n";
}

function verifyMath(array $variants, array &$errors): void
{
    echo "\033[1mMath verification (LR1):\033[0m\n";

    $errorVariants = 0;

    foreach ($variants as $name => $data) {
        $issues = [];
        $group = $data['group'] ?? 'A';

        // Currency conversion per group
        switch ($group) {
            case 'A':
                // UAH → USD: amount / rate
                if (isset($data['amount'], $data['rate'], $data['conversion_result'])) {
                    $expected = round($data['amount'] / $data['rate'], 2);
                    if (abs($expected - $data['conversion_result']) > 0.01) {
                        $issues[] = "currency: {$data['amount']}/{$data['rate']} = {$expected}, file says {$data['conversion_result']}";
                    }
                }
                break;

            case 'B':
                // USD → UAH: amount * rate
                if (isset($data['amount'], $data['rate'], $data['conversion_result'])) {
                    $expected = round($data['amount'] * $data['rate'], 2);
                    if (abs($expected - $data['conversion_result']) > 0.01) {
                        $issues[] = "currency: {$data['amount']}*{$data['rate']} = {$expected}, file says {$data['conversion_result']}";
                    }
                }
                break;

            case 'C':
                // UAH → EUR with commission: (amount / rate) then * (1 - commission/100)
                if (isset($data['amount'], $data['rate'], $data['commission'])) {
                    $before = round($data['amount'] / $data['rate'], 2);
                    $after = round($before * (1 - $data['commission'] / 100), 2);

                    if (isset($data['conversion_before']) && abs($before - $data['conversion_before']) > 0.01) {
                        $issues[] = "currency_before: {$data['amount']}/{$data['rate']} = {$before}, file says {$data['conversion_before']}";
                    }
                    if (isset($data['conversion_after']) && abs($after - $data['conversion_after']) > 0.01) {
                        $issues[] = "currency_after: {$before}*(1-{$data['commission']}%) = {$after}, file says {$data['conversion_after']}";
                    }
                }
                break;
        }

        // 3-digit number operations (same for all groups)
        if (isset($data['number'])) {
            $num = $data['number'];
            $d1 = intdiv($num, 100);
            $d2 = intdiv($num, 10) % 10;
            $d3 = $num % 10;

            // Digit sum
            $expectedSum = $d1 + $d2 + $d3;
            if (isset($data['digit_sum']) && $data['digit_sum'] !== $expectedSum) {
                $issues[] = "digit_sum: {$d1}+{$d2}+{$d3} = {$expectedSum}, file says {$data['digit_sum']}";
            }

            // Reversed
            $expectedReversed = $d3 * 100 + $d2 * 10 + $d1;
            if (isset($data['reversed']) && $data['reversed'] !== $expectedReversed) {
                $issues[] = "reversed: expected {$expectedReversed}, file says {$data['reversed']}";
            }

            // Max arrangement (Sub1)
            $digits = [$d1, $d2, $d3];
            rsort($digits);
            $expectedMax = $digits[0] * 100 + $digits[1] * 10 + $digits[2];
            if (isset($data['max_number']) && $data['max_number'] !== $expectedMax) {
                $issues[] = "max_number: expected {$expectedMax}, file says {$data['max_number']}";
            }

            // Min arrangement (Sub2)
            $digitsMin = [$d1, $d2, $d3];
            sort($digitsMin);
            $expectedMin = $digitsMin[0] * 100 + $digitsMin[1] * 10 + $digitsMin[2];
            if (isset($data['min_number']) && $data['min_number'] !== $expectedMin) {
                $issues[] = "min_number: expected {$expectedMin}, file says {$data['min_number']}";
            }

            // Palindrome (Sub3)
            $expectedPalindrome = ($num === $expectedReversed) ? 'так' : 'ні';
            if (isset($data['palindrome']) && $data['palindrome'] !== $expectedPalindrome) {
                $issues[] = "palindrome: expected {$expectedPalindrome}, file says {$data['palindrome']}";
            }
        }

        if (empty($issues)) {
            // Show sample variants and group headers
            $showAlways = ['v1', 'v10', 'v11', 'v15', 'v20', 'v21', 'v25', 'v30'];
            if (in_array($name, $showAlways)) {
                echo "  \033[32m✓\033[0m {$name} (Group {$group}): all math correct\n";
            }
        } else {
            $errorVariants++;
            foreach ($issues as $issue) {
                $errors[] = "{$name}: {$issue}";
                echo "  \033[31m✗\033[0m {$name} (Group {$group}): {$issue}\n";
            }
        }
    }

    // Summary line
    $totalVariants = count($variants);
    $passedVariants = $totalVariants - $errorVariants;
    echo "  \033[32m✓\033[0m Math: {$passedVariants}/{$totalVariants} variants correct\n";

    // Group summary
    $groups = ['A' => 0, 'B' => 0, 'C' => 0];
    foreach ($variants as $data) {
        $groups[$data['group'] ?? 'A']++;
    }
    echo "  Groups: A=" . $groups['A'] . " B=" . $groups['B'] . " C=" . $groups['C'] . "\n";
    echo "\n";
}

function verifyMathLR2(array $variants, array &$errors): void
{
    echo "\033[1mMath verification (LR2):\033[0m\n";

    $errorVariants = 0;

    foreach ($variants as $name => $data) {
        $issues = [];
        $subgroup = $data['subgroup'] ?? 1;

        // Date difference
        if (isset($data['date1'], $data['date2'], $data['days'])) {
            $d1 = DateTime::createFromFormat('d-m-Y', $data['date1']);
            $d2 = DateTime::createFromFormat('d-m-Y', $data['date2']);
            if ($d1 && $d2) {
                $expectedDays = (int)abs($d1->diff($d2)->days);
                if ($expectedDays !== $data['days']) {
                    $issues[] = "days: expected {$expectedDays}, file says {$data['days']}";
                }

                // Sub2: weeks + remainder
                if ($subgroup === 2 && isset($data['weeks'], $data['remainder_days'])) {
                    $expectedWeeks = intdiv($data['days'], 7);
                    $expectedRemainder = $data['days'] % 7;
                    if ($expectedWeeks !== $data['weeks']) {
                        $issues[] = "weeks: expected {$expectedWeeks}, file says {$data['weeks']}";
                    }
                    if ($expectedRemainder !== $data['remainder_days']) {
                        $issues[] = "remainder_days: expected {$expectedRemainder}, file says {$data['remainder_days']}";
                    }
                }

                // Sub3: weekday verification
                if ($subgroup === 3 && isset($data['weekdays'])) {
                    $ukDays = [1 => 'понеділок', 2 => 'вівторок', 3 => 'середа', 4 => 'четвер', 5 => "п'ятниця", 6 => 'субота', 7 => 'неділя'];
                    $wd1 = $ukDays[(int)$d1->format('N')] ?? '?';
                    $wd2 = $ukDays[(int)$d2->format('N')] ?? '?';
                    $expected = "{$wd1} — {$wd2}";
                    if ($data['weekdays'] !== $expected) {
                        $issues[] = "weekdays: expected '{$expected}', file says '{$data['weekdays']}'";
                    }
                }
            }
        }

        // Password mode per subgroup
        $expectedMode = match ($subgroup) {
            2 => 'best_of_3',
            3 => 'no_login',
            default => 'single',
        };
        if (isset($data['password_mode']) && $data['password_mode'] !== $expectedMode) {
            $issues[] = "password_mode: expected {$expectedMode}, file says {$data['password_mode']}";
        }

        if (empty($issues)) {
            $showAlways = ['v1', 'v5', 'v9', 'v11', 'v14', 'v18', 'v21', 'v24', 'v28'];
            if (in_array($name, $showAlways)) {
                $group = $data['group'] ?? 'A';
                echo "  \033[32m✓\033[0m {$name} (Group {$group}, Sub{$subgroup}): all checks correct\n";
            }
        } else {
            $errorVariants++;
            $group = $data['group'] ?? 'A';
            foreach ($issues as $issue) {
                $errors[] = "{$name}: {$issue}";
                echo "  \033[31m✗\033[0m {$name} (Group {$group}, Sub{$subgroup}): {$issue}\n";
            }
        }
    }

    $totalVariants = count($variants);
    $passedVariants = $totalVariants - $errorVariants;
    echo "  \033[32m✓\033[0m Math: {$passedVariants}/{$totalVariants} variants correct\n";

    // Subgroup summary
    $subs = [1 => 0, 2 => 0, 3 => 0];
    foreach ($variants as $data) {
        $subs[$data['subgroup'] ?? 1]++;
    }
    echo "  Subgroups: Sub1=" . $subs[1] . " Sub2=" . $subs[2] . " Sub3=" . $subs[3] . "\n";
    echo "\n";
}
