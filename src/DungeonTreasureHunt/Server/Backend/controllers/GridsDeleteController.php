<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;

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
            $username = $user['username'];

            $idToDelete = $this->validateAndGetId($request);
            $this->deleteGrid($username, $idToDelete);

            return JsonResponseBuilder::success();
        } catch (InvalidTokenException $e) {
            return JsonResponseBuilder::unauthorized($e->getMessage());
        } catch (InvalidRequestException $e) {
            return JsonResponseBuilder::badRequest($e->getMessage());
        } catch (GridNotFoundException $e) {
            return JsonResponseBuilder::notFound($e->getMessage());
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
