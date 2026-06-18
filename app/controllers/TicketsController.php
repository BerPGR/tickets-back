<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class TicketsController
{
    /** @var Engine */
    protected Engine $app;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->app = \Flight::app();
    }

    public function show() {
        $tickets = \app\models\Tickets::all();
        $this->app->json($tickets);
    }

    public function store() {
        try {
            $data = $this->app->request()->data->getData();
            $dueDate = implode("-", explode("/", $data['dueDate']));
            $ticket = [
                "owner_id" => $data['dueUser']['id'],
                "user_id" => $data['dueUser']['id'],
                "title" => $data["titulo"],
                "description" => $data['description'],
                "due_date" => $dueDate,
                'priority' => $data['priority']['value'],
                "client_id" => $data['client']['id'],
                "team_id" => $data['selectedTeam']['id']
            ];
            \app\models\Tickets::create($ticket);
            $this->app->json(['status' => 201]);
        } catch (\Throwable $e) {
            $this->app->error($e);
        }
    }
}
