<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Framework\http\ApiResponse;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Framework\services\AuthenticatedUserExtractor;
use Exception;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\http\Response;

class GridsPostController
{
    private AuthenticatedUserExtractor $authenticatedUserExtractor;
    private GridRepository $gridRepository;

    public function __construct(
        AuthenticatedUserExtractor $authenticatedUserExtractor,
        GridRepository             $gridRepository
    )
    {
        $this->authenticatedUserExtractor = $authenticatedUserExtractor;
        $this->gridRepository = $gridRepository;

    }

    public function __invoke(Request $request): Response
    {
        try {
            $user = $this->authenticatedUserExtractor->extractUser($request);
            $gridData = $this->processRequestData($request);
            $this->saveGrid($user->name, $gridData);

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