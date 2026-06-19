<?php

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->safeLoad();

return [
    "jwt_secret" => $_ENV['JWT_SECRET'] ?? '8f5ea5ef340bad436da9ca6c67ab492d8f5ab5855a7e690ad93875b92e8dc9d6',
    "jwt_expiration" => 3600,
    'jwt_algorithm' => "HS256"
];