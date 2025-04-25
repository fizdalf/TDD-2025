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
        $response = $this->createBaseResponse();

        $username = $this->authenticateUser($request, $response);
        if (!$username) {
            return $response;
        }

        $grids = $this->loadUserGrids($username);

        return $this->createSuccessResponse($grids);
    }

    private function createBaseResponse(): Response
    {
        $response = new Response();
        $response->setHeader("Content-Type", "application/json");
        return $response;
    }

    private function authenticateUser(Request $request, Response &$response): string|false
    {
        $authHeader = $request->getHeaders('Authorization') ?? '';

        if (!str_starts_with($authHeader, "Bearer ")) {
            $response->withStatus(401)->withJson(["error" => "Token no proporcionado"]);
            return false;
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            $response->withStatus(401)->withJson(["error" => "Token inválido o expirado"]);
            return false;
        }

        return $username;
    }

    private function loadUserGrids(string $username): array
    {
        $repo = $this->createRepository($username);
        return $repo->loadGrids();
    }

    private function createRepository(string $username): GridFileSystemRepository
    {
        return new GridFileSystemRepository($username);
    }

    private function createSuccessResponse(array $grids): Response
    {
        $response = $this->createBaseResponse();
        return $response->withJson(["success" => true, "grids" => $grids]);
    }
}