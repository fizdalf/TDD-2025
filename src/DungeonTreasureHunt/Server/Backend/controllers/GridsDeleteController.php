<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepositoryFactory;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilderAdapter;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;

class GridsDeleteController
{
    private JWTUserExtractor $jwtUserExtractor;
    private GridRepository $gridRepository;


    public function __construct(
        JWTUserExtractor $jwtUserExtractor,
        GridRepository   $gridRepository,
    )
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
        $this->gridRepository = $gridRepository;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->authenticateUser($request);
            $idToDelete = $this->validateAndGetId($request);
            $this->deleteGrid($username, $idToDelete);

            return JsonResponseBuilder::success();
        } catch (InvalidTokenException $e) {
            return JsonResponseBuilder::error($e->getMessage(), 401);
        } catch (InvalidRequestException $e) {
            return JsonResponseBuilder::error($e->getMessage(), 400);
        } catch (GridNotFoundException $e) {
            return JsonResponseBuilder::error($e->getMessage(), 404);
        }
    }

    private function authenticateUser(Request $request): string
    {
        $authHeader = $request->getHeaders('Authorization');

        if (!$authHeader) {
            throw new InvalidTokenException("Token no proporcionado");
        }

        $token = str_replace("Bearer ", "", $authHeader);
        $username = $this->jwtUserExtractor->extractUsername($token);

        if (!$username) {
            throw new InvalidTokenException("Token no proporcionado o inválido");
        }

        return $username;
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
            throw new GridNotFoundException("");
        }

        $this->gridRepository->deleteGrid($foundGrid);
    }
}