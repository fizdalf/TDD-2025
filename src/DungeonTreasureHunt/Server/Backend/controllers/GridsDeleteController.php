<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use Exception;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../exceptions/InvalidTokenException.php';
require_once __DIR__ . '/../exceptions/InvalidRequestException.php';
require_once __DIR__ . '/../exceptions/GridNotFoundException.php';

class GridsDeleteController
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
            $idToDelete = $this->validateAndGetId($request);
            $this->deleteGrid($username, $idToDelete);

            return $this->createSuccessResponse();
        } catch (InvalidTokenException $e) {
            return $this->handleAuthError($e->getMessage());
        } catch (InvalidRequestException $e) {
            return $this->handleRequestError($e->getMessage());
        } catch (GridNotFoundException $e) {
            return $this->handleNotFoundError($e->getMessage());
        } catch (Exception $e) {
            return $this->handleGenericError($e->getMessage());
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
        $repo = $this->createRepository($username);

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

    private function createRepository(string $username): GridFileSystemRepository
    {
        return new GridFileSystemRepository($username);
    }

    private function createSuccessResponse(): Response
    {
        return JsonResponseBuilder::success(["success" => true]);
    }

    private function handleAuthError(string $message): Response
    {
        return JsonResponseBuilder::error($message, 401);
    }

    private function handleRequestError(string $message): Response
    {
        return JsonResponseBuilder::error($message, 400);
    }

    private function handleNotFoundError(string $message): Response
    {
        return JsonResponseBuilder::error($message, 404);
    }

    private function handleGenericError(string $message): Response
    {
        return JsonResponseBuilder::error($message, 500);
    }
}