<?php

namespace App\Repositories;

use App\Models\AccessToken;

class AccessTokenRepository
{
    public function findExist(String $client_id, Int $user_id)
    {
        return AccessToken::where('client_id', $client_id)
                            ->where('user_id', $user_id)
                            ->where('revoked', 0)
                            ->where('expires_at', '>', time())
                            ->first();
    }

    public function update($id, $params)
    {
        $model = AccessToken::find($id);
        $model->update($params);
    }
}
