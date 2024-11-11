<?php

function autoLoader($class): void
{
    $baseDir = __DIR__ . '/';

    $fileName = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($fileName)) {
        require_once $fileName;
    } else {
        error_log("Autoload error: file $fileName not found for class $class");
    }
}

spl_autoload_register('autoLoader');