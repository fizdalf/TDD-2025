<?php

namespace DungeonTreasureHunt\Backend;

use JwtHandler;
use DungeonTreasureHunt\Backend\Response;

class GridsPostController
{
    public function __invoke(): void
    {
        $response = new Response();
        $headers = getallheaders();
        $input = file_get_contents("php://input");

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

        $data = json_decode($input, true);
        if (!isset($data['grid'], $data['gridName'])) {
            $response->setStatusCode(400);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Faltan datos"]));
            $response->send();
            return;
        }

        $path = __DIR__ . "/{$userData['username']}_gridSaved.txt";
        $storedGrids = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;

        $storedGrids[$newId] = ["gridName" => $data['gridName'], "grid" => $data['grid']];

        if (file_put_contents($path, json_encode($storedGrids))) {
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["success" => true]));
        } else {
            $response->setStatusCode(500);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "No se pudo guardar"]));
        }
        $response->send();
    }
}