<?php

namespace App\Http\Controllers;

use App\Repositories\AccessTokenRepository;

class AccessTokenController extends Controller
{
    protected AccessTokenRepository $accessTokenRepo;

    public function __construct (
        AccessTokenRepository $accessTokenRepo
    )
    {
        $this->accessTokenRepo = $accessTokenRepo;
    }

    public function findExist($client_id, $user_id)
    {
        return $this->accessTokenRepo->findExist($client_id, $user_id);
    }

    public function revoke(String $id)
    {
        return $this->accessTokenRepo->update($id, ['revoked' => 1]);
    }

}
