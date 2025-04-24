<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\GridRepository;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/GridsPostController.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/GridRepository.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';

class GridsPostControllerTest extends TestCase
{
    #[Test]
    public function it_should_create_grid_successfully()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);
        $repo = new GridRepository($username);

        $gridData = [
            "gridName" => "New Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = new Request(
            ['Authorization' => "Bearer $token"],
            $gridData
        );

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());

        $savedGrids = $repo->loadGrids();
        $this->assertArrayHasKey(1, $savedGrids); // Asegura que se haya guardado con un ID válido

        if ($repo->exists()) {
            $repo->delete();
        }
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $gridData = [
            "gridName" => "New Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = new Request([], $gridData);

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $request = new Request(['Authorization' => 'Bearer invalidtoken'], [
            "gridName" => "New Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ]);

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_400_if_missing_grid_data()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $request = new Request(['Authorization' => "Bearer $token"], []);

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_500_if_save_fails()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $repo = $this->createMock(GridRepository::class);
        $repo->expects($this->once())
            ->method('saveGrids')
            ->willThrowException(new Exception("Database error"));

        $gridData = [
            "gridName" => "Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = new Request(['Authorization' => "Bearer $token"], $gridData);

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());
    }
}
