<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use function json_encode;
use function json_encode as json_encode1;

require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/DungeonTreasureHuntExplorer.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';

class PlayController
{
    public function __invoke(): Response
    {
        $input = json_decode(file_get_contents("php://input"), true);

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