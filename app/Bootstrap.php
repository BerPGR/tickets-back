<?php

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__. "/../");
$dotenv->safeLoad();

Flight::set('config', [
    'origin' => 'http://localhost:4300',
    'db' => [
        'driver' => $_ENV['DB_DRIVER'],
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_NAME'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD']
    ]
]);

Flight::before('start', function () {
    header("Access-Control-Allow-Origin: *");
    header("Access-Controll-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        exit();
    }
});

$config = Flight::get('config');

Flight::register('db', \flight\database\SimplePdo::class, [
    "{$config['db']['driver']}:unix_socket=/var/run/mysqld/mysqld.sock;dbname={$config['db']['database']}", $config['db']['username'], $config['db']['password'], [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
]);

require_once __DIR__ . "/Routes.php";