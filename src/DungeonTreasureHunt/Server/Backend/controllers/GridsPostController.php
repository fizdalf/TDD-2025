<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use Exception;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/GridRepository.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../exceptions/InvalidTokenException.php';
require_once __DIR__ . '/../exceptions/InvalidRequestException.php';

class GridsPostController
{
    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor, private readonly GridRepository $gridRepository)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->processAuthentication($request);
            $gridData = $this->processRequestData($request);
            $this->saveGrid($username, $gridData);

            return $this->createSuccessResponse();
        } catch (InvalidTokenException) {
            return $this->handleAuthenticationError();
        } catch (InvalidRequestException) {
            return $this->handleInvalidRequestError();
        } catch (Exception) {
            return $this->handleSaveError();
        }
    }

    private function processAuthentication(Request $request): string
    {
        $user = $this->getAuthenticatedUser($request);
        return $user['username'];
    }

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

    private function createSuccessResponse(): Response
    {
        return JsonResponseBuilder::success(["success" => true]);
    }

    private function handleAuthenticationError(): Response
    {
        return JsonResponseBuilder::unauthorized("Token no proporcionado o mal formado");
    }

    private function handleInvalidRequestError(): Response
    {
        return JsonResponseBuilder::error("Faltan datos", 400);
    }

    private function handleSaveError(): Response
    {
        return JsonResponseBuilder::error("No se pudo guardar", 500);
    }

    public function getAuthenticatedUser(Request $request): ?array
    {
        $authHeader = $request->getHeaders('Authorization') ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            throw new InvalidTokenException('Invalid Token');
        }

        $token = substr($authHeader, 7);
        $user = $this->jwtUserExtractor->extractUserInfo($token);
        if (!isset($user) || !isset($user['username'])) {
            throw new InvalidTokenException('Invalid Token');
        }
        return $user;
    }

    public function validateRequest(array $input): void
    {
        if (!isset($input['grid'], $input['gridName'])) {
            throw new InvalidRequestException();
        }
    }
}