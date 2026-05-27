<?php

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

define('ROOT_DIR', dirname(__DIR__));
define('CLASSES_DIR', ROOT_DIR . '/classes');
define('CONTROLLERS_DIR', ROOT_DIR . '/controllers');
define('VIEWS_DIR', ROOT_DIR . '/views');
define('DATA_DIR', ROOT_DIR . '/data');

spl_autoload_register(function (string $className): void {
    $paths = [
        CLASSES_DIR . '/' . $className . '.php',
        CONTROLLERS_DIR . '/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Development debug mode: set APP_DEBUG=1 in environment to enable error display
$appDebug = getenv('APP_DEBUG');
if ($appDebug === '1' || strtolower($appDebug) === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}
