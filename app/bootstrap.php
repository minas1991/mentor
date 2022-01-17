<?php
define('ROOT_DIR', __DIR__ . '/../');

spl_autoload_register(function ($className) {
    // App prefix
    $prefix = 'App\\';

    // base app directory for the namespace prefix
    $base_dir = __DIR__;

    // check if class use the namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }

    // get class name
    $relative_class = substr($className, $len);

    // replace the namespace prefix with the base directory
    $file = $base_dir . '/' . str_replace('\\', '/', $relative_class) . '.php';

    // check if file exists and require
    if (file_exists($file)) {
        require $file;
    }
});
