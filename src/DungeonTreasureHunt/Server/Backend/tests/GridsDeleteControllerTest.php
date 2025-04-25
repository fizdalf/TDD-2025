<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/GridsDeleteController.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../services/GridRepository.php';

class GridsDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_should_delete_grid_successfully()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);
        $repo = new GridFileSystemRepository($username);
        $repo->saveGrids([1 => ["gridName" => "Test Grid", "grid" => [[0, 1], [1, 0]]]]);

        $request = new Request(
            ['Authorization' => "Bearer $token"],
            ['id' => 1]
        );

        $controller = new GridsDeleteController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());

        if ($repo->exists()) {
            $repo->delete();
        }
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $request = new Request([], ['id' => 1]);

        $controller = new GridsDeleteController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $request = new Request(['Authorization' => 'Bearer invalidtoken'], ['id' => 1]);

        $controller = new GridsDeleteController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_400_if_id_missing()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $request = new Request(['Authorization' => "Bearer $token"], []);

        $controller = new GridsDeleteController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_404_if_grid_does_not_exist()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $repo = new GridFileSystemRepository($username);
        $repo->saveGrids([]);

        $request = new Request(['Authorization' => "Bearer $token"], ['id' => 999]);

        $controller = new GridsDeleteController(new JWTUserExtractor(new JwtHandler()));
        $response = $controller($request);

        $this->assertEquals(404, $response->getStatus());

        if ($repo->exists()) {
            $repo->delete();
        }
    }
}