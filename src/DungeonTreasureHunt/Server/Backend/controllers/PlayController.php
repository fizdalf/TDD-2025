<?php

namespace DungeonTreasureHunt\Backend;

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\Response;
use function json_encode;
use function json_encode as json_encode1;

class PlayController
{
    public function __invoke(): Response
    {
        $response = new Response();
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            $response->setStatusCode(400);
            $response->setHeader("Content-Type", "application/json");
            $response->setBody(json_encode1(["error" => "No se pudo procesar el grid"]));
            return $response;
        }

        $explorer = new DungeonTreasureHuntExplorer();
        $path = $explorer->findPathToTreasure($input);
        $response->setHeader("Content-Type", "application/json");
        $response->setBody(json_encode($path));
        return $response;
    }
}