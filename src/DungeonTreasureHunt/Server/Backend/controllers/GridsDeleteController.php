<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\ApiResponse;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\http\Response;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;

class GridsDeleteController
{
    private AuthenticatedUserExtractor $authenticatedUserExtractor;
    private GridRepository $gridRepository;

    public function __construct(
        AuthenticatedUserExtractor $authenticatedUserExtractor,
        GridRepository $gridRepository
    ) {
        $this->authenticatedUserExtractor = $authenticatedUserExtractor;
        $this->gridRepository = $gridRepository;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $user = $this->authenticatedUserExtractor->extractUser($request);
            $idToDelete = $this->validateAndGetId($request);
            $this->deleteGrid($user->name, $idToDelete);

            return APIResponse::success([]);
        } catch (InvalidTokenException $e) {
            return APIResponse::error($e->getMessage(), 401);
        } catch (InvalidRequestException $e) {
            return APIResponse::error($e->getMessage(), 400);
        } catch (GridNotFoundException $e) {
            return APIResponse::error($e->getMessage(), 404);
        }
    }

    private function validateAndGetId(Request $request): string
    {
        $idToDelete = $request->getParams('id');
        if ($idToDelete === null) {
            throw new InvalidRequestException("ID no proporcionado");
        }

        return $idToDelete;
    }

    private function deleteGrid(string $username, string $idToDelete): void
    {
        $foundGrid = $this->gridRepository->getGrid($username, $idToDelete);

        if (!$foundGrid) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        $this->gridRepository->deleteGrid($foundGrid);
    }
}
