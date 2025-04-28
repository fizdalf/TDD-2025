<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use Exception;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

class GridsGetController
{
    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->authenticateUser($request);
            $grids = $this->loadUserGrids($username);

            return JsonResponseBuilder::success(["grids" => $grids]);
        } catch (InvalidTokenException $e) {
            return $this->handleAuthError($e->getMessage());
        } catch (Exception) {
            return $this->handleGenericError();
        }
    }

    private function authenticateUser(Request $request): string
    {
        $authHeader = $request->getHeaders('Authorization') ?? '';

        if (!str_starts_with($authHeader, "Bearer ")) {
            throw new InvalidTokenException("Token no proporcionado");
        }

        $token = substr($authHeader, 7);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            throw new InvalidTokenException("Token inválido o expirado");
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

    private function handleAuthError(string $message): Response
    {
        return JsonResponseBuilder::unauthorized($message);
    }

    private function handleGenericError(): Response
    {

        return JsonResponseBuilder::internalServerError();
    }
}