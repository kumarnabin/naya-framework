<?php

use Konnect\NayaFramework\Controllers\UserController;
use Konnect\NayaFramework\Lib\Route;
use Konnect\NayaFramework\Lib\View;
use Konnect\NayaFramework\Models\User;

// Define routes
Route::get('/', function () {
    View::render('index', [
        'name' => 'John Doe',
        'message' => 'Hello, World!',
        'users' => (new User)->all()
    ]);
});
// Define routes
Route::get('/cc', function () {
    try {
        if (!file_exists(__DIR__ . '/../../cache')) {
            mkdir(__DIR__ . '/../../cache', 0777, true);
        }
        if (file_exists(__DIR__ . '/../../cache/routes.php')) {
            unlink(__DIR__ . '/../../cache/routes.php');
            echo "Cache cleared!";
        } else {
            echo "Cache not found!";
        }
    } catch (Exception $e) {
        echo "Error clearing cache: " . $e->getMessage();
    }
});

Route::get('/refresh-users', [UserController::class, 'refresh']);
Route::resource('/users', UserController::class);
