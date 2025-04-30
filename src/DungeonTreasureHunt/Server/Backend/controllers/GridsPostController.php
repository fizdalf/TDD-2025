<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;
use Exception;

class GridsPostController
{
    private JWTUserExtractor $jwtUserExtractor;
    private GridRepository $gridRepository;
    private ResponseBuilder $responseBuilder;

    public function __construct(
        JWTUserExtractor $jwtUserExtractor,
        GridRepository $gridRepository,
        ResponseBuilder $responseBuilder
    ) {
        $this->jwtUserExtractor = $jwtUserExtractor;
        $this->gridRepository = $gridRepository;
        $this->responseBuilder = $responseBuilder;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->processAuthentication($request);
            $gridData = $this->processRequestData($request);
            $this->saveGrid($username, $gridData);

            return $this->responseBuilder->success();
        } catch (InvalidTokenException) {
            return $this->handleAuthenticationError();
        } catch (InvalidRequestException) {
            return $this->handleInvalidRequestError();
        } catch (Exception) {
            return $this->handleSaveError();
        }
    }

    /**
     * @throws InvalidTokenException
     */
    private function processAuthentication(Request $request): string
    {
        $user = $this->getAuthenticatedUser($request);
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

    private function handleAuthenticationError(): Response
    {
        return $this->responseBuilder->unauthorized("Token no proporcionado o mal formado");
    }

    private function handleInvalidRequestError(): Response
    {
        return $this->responseBuilder->error("Faltan datos", 400);
    }

    private function handleSaveError(): Response
    {
        return $this->responseBuilder->error("No se pudo guardar", 500);
    }

    /**
     * @throws InvalidTokenException
     */
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