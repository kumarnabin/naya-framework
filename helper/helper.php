<?php

use Dotenv\Dotenv;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('dd')) {
    #[NoReturn] function dd(...$args): void
    {
        // Loop through all the arguments
        echo '<pre>';
        foreach ($args as $arg) {
            // Dump the variable using var_dump
            var_dump($arg);
            echo PHP_EOL;  // Add a new line for better readability
        }
        echo '</pre>';

        // Stop the script after dumping the variables
        die();
    }
}
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        return $_ENV[$key] ?? $default;
    }
}
