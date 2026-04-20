<?php
// Temporary web endpoint to initialize DB using the running PHP process (useful if CLI lacks PDO drivers)
chdir(__DIR__);
require_once __DIR__ . '/tools/init_db.php';
echo "\n-- reinit_db finished --\n";
