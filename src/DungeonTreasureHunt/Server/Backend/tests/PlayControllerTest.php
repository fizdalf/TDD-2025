<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/PlayController.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/DungeonTreasureHuntExplorer.php';
require_once __DIR__ . '/../services/Response.php';

class PlayControllerTest extends TestCase
{
    #[Test]
    public function it_should_return_path_when_valid_grid_is_provided()
    {
        $mockRequest = $this->createMock(Request::class);

        $mockRequest->method('getBody')->willReturn([
            ['P', '.', '.'],
            ['#', '#', '.'],
            ['.', '.', 'T']
        ]);

        $controller = new PlayController();
        $response = $controller($mockRequest);

        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertNotEmpty($body);
    }

    #[Test]
    public function it_should_return_400_if_no_body_provided()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn([]);


        $controller = new PlayController();
        $response = $controller($mockRequest);

        $this->assertEquals(400, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('error', $body);
        $this->assertEquals('No se pudo procesar el grid', $body['error']);
    }
}
