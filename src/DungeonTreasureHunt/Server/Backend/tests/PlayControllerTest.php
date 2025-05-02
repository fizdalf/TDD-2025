<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\PlayController;
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

        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertNotEmpty($body);

    }

    #[Test]
    public function it_should_return_400_if_no_body_provided()
    {
        $request = new Request([], [], json_encode([]));

        $controller = new PlayController(new DungeonTreasureHuntExplorer());
        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());

        $expectedBody = [
            'error' => 'No se pudo procesar el grid'
        ];

        $actualBody = json_decode($response->getBody(), true);
        $this->assertEquals($expectedBody, $actualBody);
    }
}
