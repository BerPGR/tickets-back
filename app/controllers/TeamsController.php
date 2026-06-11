<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\models\Teams;

class TeamsController
{
    protected Engine $app;

    public function __construct()
    {
        $this->app = \Flight::app();
    }

    public function getById(int $id)
    {
        try {
            $team = Teams::find($id)->toArray();
            $this->app->json($team, 200);
        } catch (\Throwable $e) {
            $this->app->error($e);
        }
    }

    public function getAll() {
        try {
            $teams = Teams::all();
            $teams = array_map(fn($team) => $team->toArray(), $teams);
            $this->app->json($teams, 200);
        } catch (\Throwable $e) {
            $this->app->error($e);
        }
    }
}
