<?php

Flight::route("GET /", function () {
    echo "<h1>Coisa linda de se ver</h1>";
});

Flight::route("GET /teams/@id", [app\controllers\TeamsController::class, 'getById']);

Flight::route("GET /teams", [app\controllers\TeamsController::class, 'getAll']);

Flight::route("GET /clients", [app\controllers\ClientsController::class, "index"]);

Flight::route("GET /users", [app\controllers\UsersController::class, "index"]);

Flight::route("GET /users/@userId/tickets", [app\controllers\TicketsController::class, "show"]);
Flight::route("POST /tickets", [app\controllers\TicketsController::class, 'store']);