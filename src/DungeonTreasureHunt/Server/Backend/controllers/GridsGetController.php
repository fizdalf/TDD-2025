<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;

use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\UserGrids;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use Exception;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

class GridsGetController
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

            $grids = $this->gridRepository->getAllGrids($username);

            return JsonResponseBuilder::success([
                "grids" => $grids->toArray()
            ]);
        } catch (InvalidTokenException $e) {
            return JsonResponseBuilder::unauthorized($e->getMessage());
        } catch (\Exception) {
            return JsonResponseBuilder::internalServerError();
        }
    }
}
