<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/GridsPostController.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';

class GridsPostControllerTest extends TestCase
{
    public function test_it_should_return_error_if_token_is_missing(): void
    {
        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $controller = new GridsPostController($jwtExtractor);

        $headers = [];
        $input = [];

        $response = $controller->__invoke($headers, $input);

        $this->assertEquals(401, $response->statusCode);
        $this->assertEquals("Token no proporcionado", $response->body["error"]);
    }

    public function test_it_should_return_error_if_token_is_invalid(): void
    {
        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $jwtExtractor->method('extractUsername')->willReturn(null);

        $controller = new GridsPostController($jwtExtractor);

        $headers = ['Authorization' => 'Bearer invalidtoken'];
        $input = [];

        $response = $controller->__invoke($headers, $input);

        $this->assertEquals(401, $response->statusCode);
        $this->assertEquals("Token inválido o expirado", $response->body["error"]);
    }

    public function test_it_should_return_error_if_missing_data(): void
    {
        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $jwtExtractor->method('extractUsername')->willReturn('samuel');

        $controller = new GridsPostController($jwtExtractor);

        $headers = ['Authorization' => 'Bearer validtoken'];
        $input = []; // falta 'grid' y 'gridName'

        $response = $controller->__invoke($headers, $input);

        $this->assertEquals(400, $response->statusCode);
        $this->assertEquals("Faltan datos", $response->body["error"]);
    }

    public function test_it_should_return_success_if_grid_is_saved(): void
    {
        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $jwtExtractor->method('extractUsername')->willReturn('samuel');

        $controller = new GridsPostController($jwtExtractor);

        $headers = ['Authorization' => 'Bearer validtoken'];
        $input = [
            'grid' => ['x', 'o', 'x'],
            'gridName' => 'Prueba'
        ];

        $filePath = __DIR__ . '/../data/samuel_gridSaved.txt';
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $response = $controller->__invoke($headers, $input);

        $this->assertEquals(200, $response->statusCode);
        $this->assertTrue($response->body['success']);
        $this->assertFileExists($filePath);

        unlink($filePath); // limpiar después del test
    }
}
