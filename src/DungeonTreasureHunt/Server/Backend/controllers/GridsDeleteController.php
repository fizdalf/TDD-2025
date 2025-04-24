<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\GridRepository;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/GridRepository.php';


class GridsDeleteController
{

    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(Request $request): ?Response
    {
        $response = new Response();
        $authHeader = $request->getHeaders('Authorization');

        if (!$authHeader) {
            return JsonResponseBuilder::error("Token no proporcionado", 401);
        }

        $token = str_replace("Bearer ", "", $authHeader);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return JsonResponseBuilder::error("Token no proporcionado o inválido", 401);
        }

        $idToDelete = $request->getParams('id');
        if ($idToDelete === null) {
            return JsonResponseBuilder::error("ID no proporcionado", 400);
        }

        $path = __DIR__ . "/../data/{$username}_gridSaved.txt";

        $repo = new GridRepository($username);
        if (!$repo->exists()) {
            return JsonResponseBuilder::error("No se encontró el archivo", 404);
        }

        $grids = $repo->loadGrids();
        if (!isset($grids[$idToDelete])) {
            return JsonResponseBuilder::error("Grid no encontrado", 404);
        }

        unset($grids[$idToDelete]);
        $repo->saveGrids($grids);

        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["success" => true]));
        return $response;
    }
}