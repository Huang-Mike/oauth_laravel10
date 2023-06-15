<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Repositories\TokenExchangeRepository;

class TokenExchangeController extends Controller
{
    protected TokenExchangeRepository $TokenExchangeRepo;

    public function __construct (TokenExchangeRepository $TokenExchangeRepo)
    {
        $this->TokenExchangeRepo = $TokenExchangeRepo;
    }

    public function generate(Int $user_id, $json)
    {
        if ($this->TokenExchangeRepo->find($user_id)) {
            $model = $this->TokenExchangeRepo->update($user_id, ['tokens' => $json]);
        } else {
            $model = $this->TokenExchangeRepo->create([
                'user_id' => $user_id,
                'tokens' => $json
            ]);
        }

        return $model;
    }

    public function createTokenGrant($user_id, $params)
    {
        try {
            $api_url = config('app.url') . '/oauth/token';
            $header = array ("content-type: application/json");
    
            $result = (new Client())->post($api_url, [
                'headers' => $header,
                'timeout' => 10,
                'form_params' => $params,
            ])->getBody()->getContents();

            DB::beginTransaction();
            $tokenExchange = self::generate($user_id, $result);
            DB::commit();

            return $tokenExchange->grant;

        } catch (\Throwable $th) {
            DB::rollBack();
            return null;
        }
    }

    public function exchange(String $grant)
    {
        return $this->TokenExchangeRepo->findByGrant($grant);
    }

}
