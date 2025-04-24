<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\Request;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/DungeonTreasureHuntExplorer.php';
require_once __DIR__ . '/../http/Request.php';

class PlayController
{
    public function __invoke(Request $request): Response
    {
        $input = $request->getBody();

        if (!$input) {
            return (new Response())
                ->withStatus(400)
                ->withJson(["error" => "No se pudo procesar el grid"]);
        }

        $explorer = new DungeonTreasureHuntExplorer();
        $path = $explorer->findPathToTreasure($input);

        return (new Response())
            ->withJson($path);
    }
}