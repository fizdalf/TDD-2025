<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';

class GridsGetController
{
    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(array $headers = []): Response
    {
        $response = new Response();
        $response->setHeader("Content-Type", "application/json");

        $authHeader = $headers['Authorization'] ?? '';

        if (!str_starts_with($authHeader, "Bearer ")) {
            return $response->withStatus(401)->withJson(["error" => "Token no proporcionado"]);
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return $response->withStatus(401)->withJson(["error" => "Token inválido o expirado"]);
        }

        $path = __DIR__ . "/../data/{$username}_gridSaved.txt";

        if (!file_exists($path)) {
            return $response->withJson(["success" => true, "grids" => []]);
        }

        $fileContent = file_get_contents($path);
        $grids = json_decode($fileContent, true) ?? [];

        return $response->withJson(["success" => true, "grids" => $grids]);
    }
}
