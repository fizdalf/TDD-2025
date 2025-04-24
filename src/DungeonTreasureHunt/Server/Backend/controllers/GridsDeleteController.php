<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';


class GridsDeleteController
{

    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke($params): ?Response
    {
        $response = new Response();
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return JsonResponseBuilder::error("Token no proporcionado", 401);
        }

        $token = str_replace("Bearer ", "", $headers['Authorization']);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return JsonResponseBuilder::error("Token no proporcionado o inválido", 401);
        }

        $idToDelete = $params['id'] ?? null;
        if ($idToDelete === null) {
            return JsonResponseBuilder::error("ID no proporcionado", 400);
        }

        $path = __DIR__ . "/../data/{$username}_gridSaved.txt";

        if (!file_exists($path)) {
            return JsonResponseBuilder::error("No se encontró el archivo", 404);
        }

        $grids = json_decode(file_get_contents($path), true);
        if (!isset($grids[$idToDelete])) {
            return JsonResponseBuilder::error("Grid no encontrado", 404);
        }

        unset($grids[$idToDelete]);
        file_put_contents($path, json_encode($grids));

        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["success" => true]));
        return $response;
    }
}