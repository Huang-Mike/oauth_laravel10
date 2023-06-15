<?php

namespace App\Http\Controllers;

use App\Repositories\ClientsRepository;

class ClientsController extends Controller
{
    protected ClientsRepository $clientsRepo;

    public function __construct(ClientsRepository $clientsRepo)
    {
        $this->clientsRepo = $clientsRepo;
    }

    public function isAvailable($client_id, $client_secret)
    {
        return $this->clientsRepo->isAvailable($client_id, $client_secret);
    }
}
