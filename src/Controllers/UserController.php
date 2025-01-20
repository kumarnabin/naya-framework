<?php

namespace Konnect\NayaFramework\Controllers;

use Konnect\NayaFramework\Database\UserSchema;
use Konnect\NayaFramework\Models\User;
use Konnect\NayaFramework\Services\GenericService;

class UserController extends RestController
{

    public function __construct()
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'username' => ['required', 'alpha_num', 'min:5', 'max:20'],
            'age' => ['required', 'numeric', 'min_value:18'],
            'dob' => ['required', 'date'],
            'confirm_password' => ['required'],
            'website' => ['url'],
        ];
        parent::__construct(new GenericService(new User(), $rules));
    }
    public function refresh()
    {
//        (new UserSchema)->dropSchema();
//        (new UserSchema)->schema();
        (new UserSchema)->seedData();
        echo "User refreshed!";
    }

}
