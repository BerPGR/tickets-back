<?php

declare(strict_types=1);

namespace app\controllers;
use flight\Engine;

class AuthController
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

    public function login()
    {
        try {

            $data = $this->app->request()->data->getData();

            $email = $data['email'];
            $password = $data['password'];

            $result = (new \app\models\Users)->select("*")->where("email", "=", $email)->limit(1)->get();
            if (!empty($result)) {
                $user = $result[0]->toArray();

                if (empty($password) || !password_verify($password, $user["password_hash"])) {
                    return $this->app->json(["message" => "Senha não coincide"], 403);
                }

                $jwtService = new \JwtSecret();

                $token = $jwtService->generate([
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);

                return $this->app->json([
                    'token' => $token,
                    'expires_in' => 3600
                ]);
            }
            $this->app->json(['message' => "Não existe usuário com esse email"], 404);

        } catch (\Throwable $e) {
            $this->app->error($e);
        }
    }
}
