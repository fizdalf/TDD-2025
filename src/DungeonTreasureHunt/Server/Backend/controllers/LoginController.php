<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';

class LoginController
{
    public function __invoke(): Response
    {
        $users = [
            "admin" => "1234",
            "user" => "abcd"
        ];

        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['username'], $input['password'])) {
            return JsonResponseBuilder::error("Faltan datos", 400);
        }

        $username = $input['username'];
        $password = $input['password'];

        if (!isset($users[$username]) || $users[$username] !== $password) {
            return JsonResponseBuilder::error("Credenciales incorrectas", 401);
        }

        $token = JwtHandler::generateToken(["username" => $username]);

        return JsonResponseBuilder::success(["token" => $token]);
    }
}
