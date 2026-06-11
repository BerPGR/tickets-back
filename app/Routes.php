<?php

Flight::route("GET /", function () {
    echo "<h1>Coisa linda de se ver</h1>";
});

Flight::route("GET /teams/@id", [app\controllers\TeamsController::class, 'getById']);