<?php

namespace App\Http\Controllers;

use App\Repositories\UserPasswordRepository;

class UserPasswordController extends Controller
{
    protected UserPasswordRepository $userPasswordRepo;

    public function __construct (
        UserPasswordRepository $userPasswordRepo
    )
    {
        $this->userPasswordRepo = $userPasswordRepo;
    }

    public function create(Array $params)
    {
        return $this->userPasswordRepo->create($params);
    }

}
