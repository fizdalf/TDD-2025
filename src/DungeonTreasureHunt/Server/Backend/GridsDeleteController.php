<?php

namespace DungeonTreasureHunt\Backend;

use JwtHandler;
use DungeonTreasureHunt\Backend\Response;


class GridsDeleteController
{
    public function __invoke($params): ?Response
    {
        $response = new Response();
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            $response->setStatusCode(401);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Token no proporcionado"]));
            return $response;
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);
        $userData = JwtHandler::verifyToken($token);

        if (!$userData) {
            $response->setStatusCode(401);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Token inv\u00e1lido o expirado"]));
            $response->send();
            return null;
        }

        $idToDelete = $params['id'] ?? null;
        if ($idToDelete === null) {
            $response->setStatusCode(400);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "ID no proporcionado"]));
            $response->send();
            return null;
        }

        $path = __DIR__ . "/../{$userData['username']}_gridSaved.txt";

        if (!file_exists($path)) {
            $response->setStatusCode(404);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "No se encontr\u00f3 el archivo"]));
            $response->send();
            return null;
        }

        $grids = json_decode(file_get_contents($path), true);
        if (!isset($grids[$idToDelete])) {
            $response->setStatusCode(404);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode(["error" => "Grid no encontrado"]));
            $response->send();
            return null;
        }

        unset($grids[$idToDelete]);
        file_put_contents($path, json_encode($grids));

        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["success" => true]));
        $response->send();
        return null;
    }
}