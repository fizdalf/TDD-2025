<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\http\APIResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlayControllerTest extends TestCase
{
    #[Test]
    public function it_should_return_path_when_valid_grid_is_provided()
    {
        $request = new Request([], [], json_encode([
            ['P', '.', '.'],
            ['#', '#', '.'],
            ['.', '.', 'T']
        ]));

        $controller = new PlayController(new DungeonTreasureHuntExplorer());
        $response = $controller($request);

        //TODO: maybe we should replace dependency with mock?
        $body = json_decode($response->getBody(), true);
        $expectedResponse = APIResponse::success($body);

        $this->assertEquals($expectedResponse, $response);

    }

    #[Test]
    public function it_should_return_400_if_no_body_provided()
    {
        $request = new Request([], [], json_encode([]));

        $controller = new PlayController(new DungeonTreasureHuntExplorer());
        $response = $controller($request);

        $expectedResponse = APIResponse::error('No se pudo procesar el grid');

        $this->assertEquals($expectedResponse, $response);
    }
}
