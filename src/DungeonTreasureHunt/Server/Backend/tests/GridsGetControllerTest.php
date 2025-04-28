<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../controllers/GridsGetController.php';

class GridsGetControllerTest extends TestCase
{


    private string $username = "Test";

    protected function setUp(): void
    {
        $this->token = JwtHandler::generateToken(["username" => $this->username]);
        $path = __DIR__ . "/../data/{$this->username}_gridSaved.txt";


        $grids = [["name" => "Grid 1"], ["name" => "Grid 2"]];
        file_put_contents($path, json_encode($grids));
    }

    #[Test]
    public function it_should_return_grids_when_authorized()
    {
        $jwtHandler = new JwtHandler();
        $token = $jwtHandler->generateToken(['username' => 'Test']);
        $extractor = new JWTUserExtractor($jwtHandler);

        $headers = ['Authorization' => 'Bearer ' . $token];
        $request = new Request($headers);

        $controller = new GridsGetController($extractor);
        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertTrue($body['status'] === "success");
        $this->assertArrayHasKey('grids', $body);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_no_token(): void
    {
        $request = new Request();
        $extractor = new JWTUserExtractor(new JwtHandler());
        $controller = new GridsGetController($extractor);

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals("Token no proporcionado", $body["error"]);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_token_is_invalid(): void
    {
        $request = new Request(["Authorization" => "Bearer tokenfalso"]);
        $extractor = new JWTUserExtractor(new JwtHandler());
        $controller = new GridsGetController($extractor);

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals("Token inválido o expirado", $body["error"]);
    }
}