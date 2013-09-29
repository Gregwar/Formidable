<?php

$vendor = __DIR__.'/vendor/autoload.php';
if (file_exists($vendor)) {
    require($vendor);
}

/**
 * Registers an autoload for all the classes
 */
spl_autoload_register(function ($className) {
    $namespace = 'Gregwar\\Formidable';

    if (strpos($className, $namespace) === 0) {
        $className = str_replace($namespace, '', $className);
        $fileName = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($fileName)) {
            require($fileName);
        }
    }
});
