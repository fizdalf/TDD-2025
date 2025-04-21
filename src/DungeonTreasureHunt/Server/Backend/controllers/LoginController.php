<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
use function json_encode;
use function json_encode as json_encode1;
use function json_encode as json_encode2;

class LoginController
{
    public function __invoke(): Response
    {
        $response = new Response();

        $users = [
            "admin" => "1234",
            "user" => "abcd"
        ];

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['username'], $input['password'])) {
            $response->setStatusCode(400);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode2(["error" => "Faltan datos"]));
            return $response;
        }

        $username = $input['username'];
        $password = $input['password'];

        if (!isset($users[$username]) || $users[$username] !== $password) {
            $response->setStatusCode(401);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode1(["error" => "Credenciales incorrectas"]));
            return $response;
        }

        $token = JwtHandler::generateToken(["username" => $username]);
        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["token" => $token]));
        return $response;
    }
}