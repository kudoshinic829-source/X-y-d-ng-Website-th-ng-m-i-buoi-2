<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/helpers.php';

spl_autoload_register(function (string $class): void {
    $directories = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/controllers/admin/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/services/',
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

new App();
