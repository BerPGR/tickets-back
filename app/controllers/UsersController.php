<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class UsersController
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

    public function index() {
        try {
            $users = \app\models\Users::all();
            $users = array_map(fn ($user) => $user->toArray(), $users);
            $this->app->json($users, 200);
        } catch (\Throwable $e) {
            $this->app->error($e);
        }
    }
}
