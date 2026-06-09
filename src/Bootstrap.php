<?php

Flight::set('config', [
    'origin' => 'http://localhost:4300',
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'tickets',
        'username' => 'root',
        'password' => ''
    ]
]);

Flight::before('start', function () {
    header("Access-Control-Allow-Origin: *");
    header("Access-Controll-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

    if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        exit();
    }
});

$config = Flight::get('config');

Flight::register('db', \flight\database\SimplePdo::class, [
    "{$config['db']['driver']}:host={$config['db']['host']};dbname={$config['db']['database']}", $config['db']['username'], "", [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
]);

require_once __DIR__ . "/Routes.php";