<?php
// Debug helper to run a specific route in lr5 variant v14 and show errors
putenv('APP_DEBUG=1');
$_GET['route'] = 'anime/view';
$_GET['id'] = '5';
chdir(__DIR__ . '/../lr5/variants/v14');
require __DIR__ . '/../lr5/variants/v14/index.php';
