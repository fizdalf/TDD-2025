<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../http/Request.php';

class LoginController
{
    public function __invoke(Request $request): Response
    {
        $users = [
            "admin" => "1234",
            "user" => "abcd"
        ];

        $body = $request->getBody();

        if (!isset($body['username'], $body['password'])) {
            return JsonResponseBuilder::error("Faltan datos", 400);
        }

        $username = $body['username'];
        $password = $body['password'];

        if (!isset($users[$username]) || $users[$username] !== $password) {
            return JsonResponseBuilder::error("Credenciales incorrectas", 401);
        }

        $token = JwtHandler::generateToken(["username" => $username]);

        return JsonResponseBuilder::success(["token" => $token]);
    }
}
