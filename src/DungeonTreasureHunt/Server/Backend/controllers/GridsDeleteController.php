<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepositoryFactory;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;

class GridsDeleteController
{
    private JWTUserExtractor $jwtUserExtractor;
    private GridRepositoryFactory $gridRepositoryFactory;
    private ResponseBuilder $responseBuilder;

    public function __construct(
        JWTUserExtractor $jwtUserExtractor,
        GridRepositoryFactory $gridRepositoryFactory,
        ResponseBuilder $responseBuilder
    ) {
        $this->jwtUserExtractor = $jwtUserExtractor;
        $this->gridRepositoryFactory = $gridRepositoryFactory;
        $this->responseBuilder = $responseBuilder;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $username = $this->authenticateUser($request);
            $idToDelete = $this->validateAndGetId($request);
            $this->deleteGrid($username, $idToDelete);

            return $this->responseBuilder->success();
        } catch (InvalidTokenException $e) {
            return $this->responseBuilder->error($e->getMessage(), 401);
        } catch (InvalidRequestException $e) {
            return $this->responseBuilder->error($e->getMessage(), 400);
        } catch (GridNotFoundException $e) {
            return $this->responseBuilder->error($e->getMessage(), 404);
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
        $repo = $this->gridRepositoryFactory->createForUser($username);

        if (!$repo->exists()) {
            throw new GridNotFoundException("No se encontró el archivo");
        }

        $grids = $repo->loadGrids();
        if (!isset($grids[$idToDelete])) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        unset($grids[$idToDelete]);
        $repo->saveGrids($grids);
    }
}