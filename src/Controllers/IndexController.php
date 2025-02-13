<?php

namespace Konnect\NayaFramework\Controllers;

use Konnect\NayaFramework\Lib\Route;
use Konnect\NayaFramework\Lib\View;
use Konnect\NayaFramework\Models\User;

class IndexController
{
    public function index(): void
    {
        View::render('index', [
            'name' => 'John Doe',
            'message' => 'Hello, World!',
            'users' => (new User)->all()
        ]);
    }

    public function routeCache(): void
    {
        Route::clearCache();
        echo "Routes cache cleared!";

    }

    public function routeRefresh(): void
    {
        Route::clearCache();
        echo "Routes cache cleared!";


    }
}
