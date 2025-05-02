<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilderAdapter;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\UserGrids;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use Exception;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

class GridsGetController
{
    private JWTUserExtractor $jwtUserExtractor;
    private GridRepository $gridRepository;


    public function __construct(
        JWTUserExtractor           $jwtUserExtractor,
        GridRepository      $gridRepository
    )
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
        $this->gridRepository = $gridRepository;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->authenticateUser($request);
            $grids = $this->loadUserGrids($username);

            return JsonResponseBuilder::success([
                "grids" => $grids->toArray()
            ]);
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

    private function loadUserGrids(string $username): UserGrids
    {
        return $this->gridRepository->getAllGrids($username);
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
