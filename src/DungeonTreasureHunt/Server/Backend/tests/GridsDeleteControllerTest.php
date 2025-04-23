<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/GridsDeleteController.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../services/Response.php';

class GridsDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_should_return_error_if_token_is_missing()
    {
        $headers = ['Authorization' => ''];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $controller = new GridsDeleteController($mockExtractor);
        $response = $controller->__invoke(['id' => 'grid123'], $headers);

        $this->assertEquals(401, $response->statusCode);
    }

    #[Test]
    public function it_should_return_error_if_id_is_missing()
    {
        $headers = ['Authorization' => 'Bearer valid_token'];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $mockExtractor->method('extractUsername')->willReturn('testuser');

        $controller = new GridsDeleteController($mockExtractor);
        $response = $controller->__invoke([], $headers);

        $this->assertEquals(400, $response->statusCode);
    }

    #[Test]
    public function it_should_return_error_if_grid_not_found()
    {
        $headers = ['Authorization' => 'Bearer valid_token'];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $mockExtractor->method('extractUsername')->willReturn('testuser');

        $controller = new GridsDeleteController($mockExtractor);
        $response = $controller->__invoke(['id' => 'non_existing_grid'], $headers);

        $this->assertEquals(404, $response->statusCode);
    }
}
