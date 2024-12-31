<?php

use Konnect\NayaFramework\Controllers\UserController;
use Konnect\NayaFramework\Lib\Route;

// Define routes
Route::get('/', function () {
    echo "Welcome to the homepage!";
});
// Define routes
Route::get('/cc', function () {
    try {
        unlink(__DIR__ . '/../../cache/routes.php');
        echo "Cache cleared!";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
});

Route::resource('/users', UserController::class);
