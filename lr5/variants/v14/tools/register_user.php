<?php
// Usage: php tools/register_user.php <login> <password> <email> <first_name> <last_name>
require __DIR__ . '/../config/init.php';
if ($argc < 6) { echo "Usage: php tools/register_user.php <login> <password> <email> <first_name> <last_name>\n"; exit(1); }
$login = $argv[1];
$pass = $argv[2];
$email = $argv[3];
$first = $argv[4];
$last = $argv[5];
try {
    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO users (login, password, email, first_name, last_name) VALUES (:login, :password, :email, :first_name, :last_name)');
    $stmt->execute([':login'=>$login, ':password'=>password_hash($pass, PASSWORD_DEFAULT), ':email'=>$email, ':first_name'=>$first, ':last_name'=>$last]);
    echo "Inserted user id: " . $db->lastInsertId() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(2);
}
