<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\ApiResponse;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\http\Response;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;

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

            return APIResponse::success([
                "grids" => $grids->toArray()
            ]);
        } catch (InvalidTokenException $e) {
            return APIResponse::error($e->getMessage(), 401);
        } catch (\Exception) {
            return APIResponse::error('Internal Server Error', 500);
        }
    }
}
