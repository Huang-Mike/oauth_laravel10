<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use App\Models\TokenExchange;

class TokenExchangeRepository
{
    public function find(Int $user_id)
    {
        return TokenExchange::find($user_id);
    }

    public function findByGrant(String $grant)
    {
        return TokenExchange::where('grant', $grant)->first();
    }

    public function create(Array $params)
    {
        $params['grant'] = Str::uuid()->toString();
        return TokenExchange::create($params);
    }

    public function update(Int $user_id, Array $params)
    {
        $model = self::find($user_id);
        $params['grant'] = Str::uuid()->toString();
        $model->update($params);
        return $model;
    }
}
