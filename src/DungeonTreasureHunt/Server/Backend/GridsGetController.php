<?php

namespace DungeonTreasureHunt\Backend;

use JwtHandler;
use DungeonTreasureHunt\Backend\Response;

class GridsGetController
{
    public function __invoke(): void
    {
        $response = new Response();
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            $response->setStatusCode(401);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Token no proporcionado"]));
            $response->send();
            return;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);
        $userData = JwtHandler::verifyToken($token);

        if (!$userData) {
            $response->setStatusCode(401);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Token inv\u00e1lido o expirado"]));
            $response->send();
            return;
        }

        $path = __DIR__ . "/{$userData['username']}_gridSaved.txt";

        if (!file_exists($path)) {
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["success" => true, "grids" => []]));
            $response->send();
            return;
        }

        $fileContent = file_get_contents($path);
        $grids = json_decode($fileContent, true) ?: [];

        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["success" => true, "grids" => $grids]));
        $response->send();
    }
}