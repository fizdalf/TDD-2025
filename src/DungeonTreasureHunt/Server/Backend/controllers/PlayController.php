<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\ApiResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\http\Response;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;

class PlayController
{
    private DungeonTreasureHuntExplorer $explorer;

    public function __construct(DungeonTreasureHuntExplorer $explorer)
    {
        $this->explorer = $explorer;
    }

    public function __invoke(Request $request): Response
    {
        $input = $this->extractGridData($request);

        if (!$this->isGridDataValid($input)) {
            return APIResponse::error("No se pudo procesar el grid", 400);
        }

        $path = $this->explorer->findPathToTreasure($input);

        return APIResponse::success(["data" => $path]);
    }

    private function extractGridData(Request $request): mixed
    {
        return $request->parseBodyAsJson();
    }

    private function isGridDataValid(mixed $input): bool
    {
        return !empty($input);
    }
}
