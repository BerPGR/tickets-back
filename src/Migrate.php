<?php

$config = Flight::get('config');

$pdo = new PDO(
    "{$config['db']['driver']}:host={$config['db']['host']};dbname={$config['db']['database']}", $config['db']['username'], $config['db']['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

$runner = new MigrationRunner($pdo);
$runner->run(__DIR__ . "/migration");