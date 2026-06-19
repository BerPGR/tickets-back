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

    public function show(int $userId) {
        try {
            $tickets = (new \app\models\Tickets)->select("
                t.id as id, title, te.name as team, status, c.name as client, priority, due_date, t.created_at as created_at, u_owner.name as owner, u_user.name as responsable
            ")
            ->join("clients c", "c.id", '=', "t.client_id")
            ->join("teams te", 'te.id', "=", "t.team_id")
            ->join("users u_owner", "t.owner_id", "=", "u_owner.id")
            ->join("users u_user", "t.user_id", "=", "u_user.id")
            ->andWhere("t.owner_id", "=", $userId)->get();
            $tickets = array_map(fn($ticket) => $ticket->toArray(), $tickets);
            $this->app->json($tickets);
        } catch (\Throwable $e) {
            $this->app->error($e);
        }
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
