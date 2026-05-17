<?php
require __DIR__ . '/../config/init.php';
$db = Database::getInstance();

$users = [
    'admin' => ['first_name'=>'Головний','last_name'=>'Адміністратор','display_name'=>'Адміністратор','email'=>'admin@anime-site.com','avatar_url'=>'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png','role'=>'admin','show_email'=>'0','notify_comments'=>'1'],
    'animefan' => ['first_name'=>'Аніме','last_name'=>'Фан','display_name'=>'Аніме Фан','email'=>'fan@example.com','avatar_url'=>'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295773_1280.png','role'=>'user','show_email'=>'0','notify_comments'=>'1'],
    'mangalover' => ['first_name'=>'Манга','last_name'=>'Любитель','display_name'=>'Манга Любитель','email'=>'manga@example.com','avatar_url'=>'https://cdn.pixabay.com/photo/2016/03/31/20/31/avatar-1295775_1280.png','role'=>'user','show_email'=>'0','notify_comments'=>'1'],
    'moderator' => ['first_name'=>'Модератор','last_name'=>'','display_name'=>'Модератор','email'=>'mod@example.com','avatar_url'=>'https://cdn.pixabay.com/photo/2016/03/31/20/27/avatar-1295772_1280.png','role'=>'moderator','show_email'=>'0','notify_comments'=>'1']
];

$upd = $db->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, display_name = :display_name, email = :email, avatar_url = :avatar_url, role = :role, show_email = :show_email, notify_comments = :notify_comments WHERE login = :login');
foreach ($users as $login => $data) {
    $params = $data;
    $params['login'] = $login;
    try {
        $upd->execute($params);
        echo "Updated user: $login\n";
    } catch (Exception $e) {
        echo "Failed to update $login: " . $e->getMessage() . "\n";
    }
}

echo "Done.\n";
