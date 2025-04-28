<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\Request;

class PlayController
{
    public function __invoke(Request $request): Response
    {
        $input = $this->extractGridData($request);

        if (!$this->isGridDataValid($input)) {
            return Response::error("No se pudo procesar el grid");
        }

        $path = $this->findPathToTreasure($input);

        return Response::success($path);
    }

    private function extractGridData(Request $request): mixed
    {
        return $request->parseBodyAsJson();
    }

    private function isGridDataValid(mixed $input): bool
    {
        return !empty($input);
    }

    private function createErrorResponse(string $message, int $statusCode = 400): Response
    {
        return JsonResponseBuilder::error($message, $statusCode);
    }

    private function findPathToTreasure(mixed $input): array
    {
        $explorer = $this->createExplorer();
        return $explorer->findPathToTreasure($input);
    }

    private function createExplorer(): DungeonTreasureHuntExplorer
    {
        return new DungeonTreasureHuntExplorer();
    }

    private function createSuccessResponse(array $path): Response
    {
        return JsonResponseBuilder::success($path);
    }
}