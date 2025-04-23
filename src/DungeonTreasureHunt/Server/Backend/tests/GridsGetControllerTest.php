<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/GridsGetController.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../services/Response.php';

class GridsGetControllerTest extends TestCase
{
    #[Test]
    public function it_should_return_error_if_token_is_missing()
    {
        $headers = ['Authorization' => ''];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $controller = new GridsGetController($mockExtractor);
        $response = $controller->__invoke($headers);

        $this->assertEquals(401, $response->statusCode);
        $this->assertEquals(['error' => 'Token no proporcionado'], json_decode($response->body, true));
    }

    #[Test]
    public function it_should_return_error_if_token_is_invalid()
    {
        $headers = ['Authorization' => 'Bearer invalid_token'];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $mockExtractor->method('extractUsername')->willReturn(null);

        $controller = new GridsGetController($mockExtractor);
        $response = $controller->__invoke($headers);

        $this->assertEquals(401, $response->statusCode);
        $this->assertEquals(['error' => 'Token inválido o expirado'], json_decode($response->body, true));
    }

    #[Test]
    public function it_should_return_empty_grids_if_file_not_exists()
    {
        $headers = ['Authorization' => 'Bearer valid_token'];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $mockExtractor->method('extractUsername')->willReturn('testuser');

        $filePath = __DIR__ . '/../data/testuser_gridSaved.txt';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $controller = new GridsGetController($mockExtractor);
        $response = $controller->__invoke($headers);

        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals(['success' => true, 'grids' => []], json_decode($response->body, true));
    }


    #[Test]
    public function it_should_return_grids_from_file()
    {
        $headers = ['Authorization' => 'Bearer valid_token'];

        $mockExtractor = $this->createMock(JWTUserExtractor::class);
        $mockExtractor->method('extractUsername')->willReturn('testuser');

        $filePath = __DIR__ . '/../data/testuser_gridSaved.txt';
        file_put_contents($filePath, json_encode(['grid1', 'grid2']));

        $controller = new GridsGetController($mockExtractor);
        $response = $controller->__invoke($headers);

        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals(['success' => true, 'grids' => ['grid1', 'grid2']], json_decode($response->body, true));

        unlink($filePath);
    }
}
