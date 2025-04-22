<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';

class GridsPostController
{

    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(): Response
    {

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if (!$authHeader) {
            return JsonResponseBuilder::unauthorized("Token no proporcionado");
        }


        if (!str_starts_with($authHeader, "Bearer ")) {
            return JsonResponseBuilder::unauthorized("Token no proporcionado o mal formado");
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return JsonResponseBuilder::unauthorized("Token inválido o expirado");
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['grid'], $input['gridName'])) {
            return JsonResponseBuilder::error("Faltan datos", 400);
        }

        $path = __DIR__ . "/../data/{$username}_gridSaved.txt";
        $storedGrids = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;

        $storedGrids[$newId] = [
            "gridName" => $input['gridName'],
            "grid" => $input['grid']
        ];

        if (file_put_contents($path, json_encode($storedGrids))) {
            return JsonResponseBuilder::success(["success" => true]);
        }

        return JsonResponseBuilder::error("No se pudo guardar", 500);
    }


}
