<?php
require __DIR__ . '/../config/init.php';
$db = Database::getInstance();
try {
    $stmt = $db->query("SELECT id, login, email, password, first_name, last_name FROM users");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
