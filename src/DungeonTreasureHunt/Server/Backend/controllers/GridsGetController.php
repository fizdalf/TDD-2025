<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Framework\http\ApiResponse;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\http\Response;
use DungeonTreasureHunt\Framework\services\AuthenticatedUserExtractor;

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
            $grids = $this->gridRepository->getAllGrids($user->name);

            return APIResponse::success([
                "grids" => $grids->toArray()
            ]);
        } catch (InvalidTokenException $e) {
            return APIResponse::error($e->getMessage(), 401);
        } catch (\Exception $e) {
            var_dump($e);
            return APIResponse::error('Internal Server Error', 500);
        }
    }
}
