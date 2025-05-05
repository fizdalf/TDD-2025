<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\http\APIResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
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

        $expectedPath = [
            ["playerPosition" => ["x" => 0, "y" => 0], "direction" => "Right"],
            ["playerPosition" => ["x" => 1, "y" => 0], "direction" => "Right"],
            ["playerPosition" => ["x" => 2, "y" => 0], "direction" => "Down"],
            ["playerPosition" => ["x" => 2, "y" => 1], "direction" => "Down"]
        ];

        $expectedResponse = APIResponse::success(["data" => $expectedPath]);
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_should_return_400_if_no_body_provided()
    {

        $explorerMock = $this->createMock(DungeonTreasureHuntExplorer::class);


        $controller = new PlayController($explorerMock);


        $request = new Request([], [], json_encode([]));


        $response = $controller($request);


        $expectedResponse = APIResponse::error('No se pudo procesar el grid');


        $this->assertEquals($expectedResponse, $response);
    }
}
