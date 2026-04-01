<?php

session_start();

define('ROOT_DIR', dirname(__DIR__));
define('CLASSES_DIR', ROOT_DIR . '/classes');
define('CONTROLLERS_DIR', ROOT_DIR . '/controllers');
define('VIEWS_DIR', ROOT_DIR . '/views');

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
