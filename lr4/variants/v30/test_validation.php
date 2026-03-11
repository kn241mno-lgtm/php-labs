<?php
/**
 * Automated test for v30 RegformController validation.
 * Run: php test_validation.php
 */

require_once __DIR__ . '/config/init.php';

$controller = new RegformController();
$validate = new ReflectionMethod($controller, 'validate');
if (PHP_VERSION_ID < 80100) {
    $validate->setAccessible(true);
}

$tests = [
    ['name' => 'Empty login', 'data' => ['login' => '', 'password' => 'test1', 'password_confirm' => 'test1'], 'expectKey' => 'login'],
    ['name' => 'Login with spaces', 'data' => ['login' => 'hello world', 'password' => 'test1', 'password_confirm' => 'test1'], 'expectKey' => 'login', 'expectContains' => 'одним словом'],
    ['name' => 'Login too short', 'data' => ['login' => 'abc', 'password' => 'test1', 'password_confirm' => 'test1'], 'expectKey' => 'login', 'expectContains' => '5 символів'],
    ['name' => 'Login with digits', 'data' => ['login' => 'hello123', 'password' => 'test1', 'password_confirm' => 'test1'], 'expectKey' => 'login', 'expectContains' => 'цифри'],
    ['name' => 'Empty password', 'data' => ['login' => 'chefdemo', 'password' => '', 'password_confirm' => ''], 'expectKey' => 'password'],
    ['name' => 'Password too short', 'data' => ['login' => 'chefdemo', 'password' => 'ab1', 'password_confirm' => 'ab1'], 'expectKey' => 'password', 'expectContains' => '5 символів'],
    ['name' => 'Password no digit', 'data' => ['login' => 'chefdemo', 'password' => 'testtest', 'password_confirm' => 'testtest'], 'expectKey' => 'password', 'expectContains' => 'одну цифру'],
    ['name' => 'Password mismatch', 'data' => ['login' => 'chefdemo', 'password' => 'test123', 'password_confirm' => 'other456'], 'expectKey' => 'password_confirm', 'expectContains' => 'не збігаються'],
    ['name' => 'All valid', 'data' => ['login' => 'chefdemo', 'password' => 'recipe1', 'password_confirm' => 'recipe1', 'about' => 'I love cooking'], 'expectEmpty' => true],
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    $errors = $validate->invoke($controller, $test['data']);

    if (!empty($test['expectEmpty'])) {
        if (empty($errors)) {
            echo "PASS: {$test['name']}\n";
            $passed++;
        } else {
            echo "FAIL: {$test['name']} — expected no errors, got: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . "\n";
            $failed++;
        }
    } else {
        $key = $test['expectKey'];
        if (!isset($errors[$key])) {
            echo "FAIL: {$test['name']} — expected error for '{$key}', got: " . json_encode($errors, JSON_UNESCAPED_UNICODE) . "\n";
            $failed++;
        } elseif (!empty($test['expectContains']) && strpos($errors[$key], $test['expectContains']) === false) {
            echo "FAIL: {$test['name']} — expected '{$test['expectContains']}' in error, got: {$errors[$key]}\n";
            $failed++;
        } else {
            echo "PASS: {$test['name']}\n";
            $passed++;
        }
    }
}

$total = $passed + $failed;
echo "\n{$passed}/{$total} tests passed.\n";

if ($failed > 0) {
    exit(1);
}
