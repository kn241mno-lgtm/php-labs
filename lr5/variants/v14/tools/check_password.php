<?php
require __DIR__ . '/../config/init.php';
$login = $argv[1] ?? null;
$pass = $argv[2] ?? null;
if (!$login || !$pass) { echo "Usage: php tools/check_password.php <login> <password>\n"; exit(1); }
$db = Database::getInstance();
$stmt = $db->prepare('SELECT password FROM users WHERE login = :login');
$stmt->execute([':login' => $login]);
$h = $stmt->fetchColumn();
if (!$h) { echo "No hash found for user\n"; exit(1); }
echo (password_verify($pass, $h) ? "OK\n" : "FAIL\n");
