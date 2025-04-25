<?php

namespace DungeonTreasureHunt\Backend\controllers;


use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/GridRepository.php';

class GridsGetController
{

    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(Request $request): Response
    {
        $response = new Response();
        $response->setHeader("Content-Type", "application/json");

        $authHeader = $request->getHeaders('Authorization') ?? '';

        if (!str_starts_with($authHeader, "Bearer ")) {
            return $response->withStatus(401)->withJson(["error" => "Token no proporcionado"]);
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return $response->withStatus(401)->withJson(["error" => "Token inválido o expirado"]);
        }

        $repo = new GridFileSystemRepository($username);
        $grids = $repo->loadGrids();

        return $response->withJson(["success" => true, "grids" => $grids]);
    }
}
