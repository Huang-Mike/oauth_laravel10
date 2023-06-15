<?php

namespace App\Repositories;

use App\Models\UserPassword;

class UserPasswordRepository
{
    public function create(Array $params)
    {
        return UserPassword::create($params);
    }
}
