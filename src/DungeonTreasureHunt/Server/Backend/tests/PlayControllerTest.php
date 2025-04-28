<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlayControllerTest extends TestCase
{
    #[Test]
    public function it_should_return_path_when_valid_grid_is_provided()
    {
        $mockRequest = $this->createMock(Request::class);

        $mockRequest->method('parseBodyAsJson')->willReturn([
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
        $mockRequest->method('parseBodyAsJson')->willReturn([]);

        $controller = new PlayController();
        $response = $controller($mockRequest);

        $this->assertEquals(400, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('error', $body);
        $this->assertEquals('No se pudo procesar el grid', $body['error']);
    }
}