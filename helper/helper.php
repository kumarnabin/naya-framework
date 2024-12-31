<?php

if (!function_exists('dd')) {
    function dd(...$args) {
        // Loop through all the arguments
        foreach ($args as $arg) {
            // Dump the variable using var_dump
            var_dump($arg);
            echo PHP_EOL;  // Add a new line for better readability
        }

        // Stop the script after dumping the variables
        die();
    }
}
