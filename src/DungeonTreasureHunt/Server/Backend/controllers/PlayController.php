<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\Request;

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
            return JsonResponseBuilder::error("No se pudo procesar el grid");
        }

        $path = $this->explorer->findPathToTreasure($input);

        return JsonResponseBuilder::success($path);
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
