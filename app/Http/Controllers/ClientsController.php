<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClientsRepository;

class ClientsController extends Controller
{
    protected ClientsRepository $clientsRepo;

    public function __construct(ClientsRepository $clientsRepo)
    {
        $this->clientsRepo = $clientsRepo;
    }

    public function list(Request $requset)
    {
        $clients = $this->clientsRepo->list();
        return view('client.list', [
            'clients' => $clients
        ]);
    }

    public function isAvailable($client_id, $client_secret)
    {
        return $this->clientsRepo->isAvailable($client_id, $client_secret);
    }
}
