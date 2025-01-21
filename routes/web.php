<?php

use Konnect\NayaFramework\Controllers\IndexController;
use Konnect\NayaFramework\Controllers\UserController;
use Konnect\NayaFramework\Lib\Route;

// Define routes
Route::get('/', [IndexController::class, 'index']);
// Define routes

Route::get('/refresh-users', [UserController::class, 'refresh']);
Route::resource('/users', UserController::class);
