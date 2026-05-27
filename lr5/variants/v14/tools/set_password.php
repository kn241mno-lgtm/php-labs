<?php
// Usage: php tools/set_password.php <login> <new_password>
// Example: php tools/set_password.php admin Admin123!

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../classes/Database.php';

if ($argc < 3) {
    echo "Usage: php tools/set_password.php <login> <new_password>\n";
    exit(1);
}

$login = $argv[1];
$newPassword = $argv[2];

try {
    $db = Database::getInstance();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare('UPDATE users SET password = :pw WHERE login = :login');
    $stmt->execute([':pw' => $hash, ':login' => $login]);

    if ($stmt->rowCount() > 0) {
        echo "Password updated for user '{$login}'.\n";
    } else {
        echo "No user found with login '{$login}', or password unchanged.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(2);
}
