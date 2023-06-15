<?php

namespace App\Repositories;

use App\Models\Clients;

class ClientsRepository
{
    /**
     * Check client is available.
     *
     * @param String $client_id
     * @param String $client_secret
     * @return object
     */
    public function isAvailable(String $client_id, String $client_secret)
    {
        return Clients::where('id', $client_id)->where('secret', $client_secret)->where('revoked', 0)->first();
    }
}
