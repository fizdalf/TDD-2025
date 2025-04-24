<?php

namespace DungeonTreasureHunt\Backend\controllers;


use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use Exception;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/GridRepository.php';
require_once __DIR__ . '/../http/Request.php';

class GridsPostController
{

    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(Request $request): Response
    {

        $authHeader = $request->getHeaders('Authorization') ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            return JsonResponseBuilder::unauthorized("Token no proporcionado o mal formado");
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return JsonResponseBuilder::unauthorized("Token inválido o expirado");
        }

        $input = $request->getBody();
        if (!isset($input['grid'], $input['gridName'])) {
            return JsonResponseBuilder::error("Faltan datos", 400);
        }

        $repo = new GridRepository($username);
        $storedGrids = $repo->loadGrids();
        $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;

        $storedGrids[$newId] = [
            "gridName" => $input['gridName'],
            "grid" => $input['grid']
        ];

        try {
            $repo->saveGrids($storedGrids);
            return JsonResponseBuilder::success(["success" => true]);
        } catch (Exception){
            return JsonResponseBuilder::error("No se pudo guardar", 500);
        }
    }


}
