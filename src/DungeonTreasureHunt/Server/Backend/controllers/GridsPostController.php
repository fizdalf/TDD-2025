<?php

namespace DungeonTreasureHunt\Backend\controllers;


use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use Exception;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/GridRepository.php';
require_once __DIR__ . '/../http/Request.php';

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
            $user = $this->getAuthenticatedUser($request);
            $username = $user['username'];
            $input = $request->parseBodyAsJson();
            $this->validateRequest($input);
            $this->gridRepository->saveGrid(new GridItem($input['gridName'], $input['grid'], $username));
            return JsonResponseBuilder::success(["success" => true]);
        } catch (InvalidTokenException) {
            return JsonResponseBuilder::unauthorized("Token no proporcionado o mal formado");
        } catch (InvalidRequest) {
            return JsonResponseBuilder::error("Faltan datos", 400);
        } catch (Exception) {
            return JsonResponseBuilder::error("No se pudo guardar", 500);
        }
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
     * @param array $input
     * @return array
     * @throws InvalidRequest
     */
    public function validateRequest(array $input): void
    {
        if (!isset($input['grid'], $input['gridName'])) {
            throw new InvalidRequest();
        }
    }


}


class InvalidTokenException extends Exception
{

}

class InvalidRequest extends Exception
{
}