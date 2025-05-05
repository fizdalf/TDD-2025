<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\ApiResponse;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\http\Response;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use Exception;

class GridsPostController
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
            $username = $this->processAuthentication($request);
            $gridData = $this->processRequestData($request);
            $this->saveGrid($username, $gridData);

            return APIResponse::success([]);
        } catch (InvalidTokenException) {
            return APIResponse::error("Token no proporcionado o mal formado", 401);
        } catch (InvalidRequestException) {
            return APIResponse::error("Faltan datos", 400);
        } catch (Exception) {
            return APIResponse::error("No se pudo guardar", 500);
        }
    }

    /**
     * @throws InvalidTokenException
     */
    private function processAuthentication(Request $request): string
    {
        $user = $this->authenticatedUserExtractor->extractUser($request);
        return $user['username'];
    }

    /**
     * @throws InvalidRequestException
     */
    private function processRequestData(Request $request): array
    {
        $input = $request->parseBodyAsJson();
        $this->validateRequest($input);
        return $input;
    }

    private function saveGrid(string $username, array $gridData): void
    {
        $this->gridRepository->saveGrid(new GridItem($gridData['gridName'], $gridData['grid'], $username));
    }


    /**
     * @throws InvalidRequestException
     */
    public function validateRequest(array $input): void
    {
        if (!isset($input['grid'], $input['gridName'])) {
            throw new InvalidRequestException();
        }
    }
}