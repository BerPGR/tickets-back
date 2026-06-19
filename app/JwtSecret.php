<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtSecret {
    private string $secret;
    private int $expiration;
    private string $algorithm;

    public function __construct() {
        $config = require __DIR__ . "/config/auth.php";
        $this->secret = $config["jwt_secret"];
        $this->expiration = $config['jwt_expiration'];
        $this->algorithm = $config['jwt_algorithm'];
    }

    public function generate(array $payload): string {
        $now = time();

        $tokenData = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + $this->expiration
        ]);

        return JWT::encode($tokenData, $this->secret, $this->algorithm);
    }

    public function validate(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) { 
            return null;
        }
    }
}