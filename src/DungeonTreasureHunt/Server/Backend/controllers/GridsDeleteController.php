<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\GridFileSystemRepository;
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
        $username = $this->authenticateUser($request);
        if (!is_string($username)) {
            return $username;
        }

        $idToDelete = $this->validateAndGetId($request);
        if (!is_string($idToDelete)) {
            return $idToDelete;
        }

        $result = $this->deleteGrid($username, $idToDelete);
        if ($result !== true) {
            return $result;
        }

        return $this->createSuccessResponse();
    }

    private function authenticateUser(Request $request): string|Response
    {
        $authHeader = $request->getHeaders('Authorization');

        if (!$authHeader) {
            return JsonResponseBuilder::error("Token no proporcionado", 401);
        }

        $token = str_replace("Bearer ", "", $authHeader);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            return JsonResponseBuilder::error("Token no proporcionado o inválido", 401);
        }

        return $username;
    }

    private function validateAndGetId(Request $request): string|Response
    {
        $idToDelete = $request->getParams('id');
        if ($idToDelete === null) {
            return JsonResponseBuilder::error("ID no proporcionado", 400);
        }

        return $idToDelete;
    }

    private function deleteGrid(string $username, string $idToDelete): bool|Response
    {
        $repo = $this->createRepository($username);

        if (!$repo->exists()) {
            return JsonResponseBuilder::error("No se encontró el archivo", 404);
        }

        $grids = $repo->loadGrids();
        if (!isset($grids[$idToDelete])) {
            return JsonResponseBuilder::error("Grid no encontrado", 404);
        }

        unset($grids[$idToDelete]);
        $repo->saveGrids($grids);

        return true;
    }

    private function createRepository(string $username): GridFileSystemRepository
    {
        return new GridFileSystemRepository($username);
    }

    private function createSuccessResponse(): Response
    {
        $response = new Response();
        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode(["success" => true]));
        return $response;
    }
}