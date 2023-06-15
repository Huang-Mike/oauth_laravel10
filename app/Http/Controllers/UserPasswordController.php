<?php

namespace App\Http\Controllers;

use App\Repositories\UserPasswordRepository;

class UserPasswordController extends Controller
{
    protected UserPasswordRepository $userPasswordCon;

    public function __construct (
        UserPasswordRepository $userPasswordCon
    )
    {
        $this->userPasswordCon = $userPasswordCon;
    }

    public function create(Array $params)
    {
        return $this->userPasswordCon->create($params);
    }

}
