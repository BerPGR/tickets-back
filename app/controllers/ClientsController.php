<?php

declare(strict_types=1);

namespace app\controllers;
use flight\Engine;

class ClientsController
{
    /** @var Engine */
    protected Engine $app;

    public function __construct()
    {
        $this->app = \Flight::app();
    }

    public function index() {
        $clients = \app\models\Clients::all();
        $clients = array_map(fn($client) => $client->toArray(), $clients);
        $this->app->json($clients, 200);
    }
}
