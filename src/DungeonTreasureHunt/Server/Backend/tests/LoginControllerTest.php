<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../http/JsonResponseBuilder.php';
require_once __DIR__ . '/../services/Response.php';
require_once __DIR__ . '/../services/JWT.php';

class LoginControllerTest extends TestCase
{

    #[Test]
    public function it_should_return_error_if_data_is_missing()
    {
        $sut = new LoginController();

        $input = json_encode([]);
        file_put_contents("php://input", $input);

        $response = $sut();

        $this->assertEquals(400, $response->statusCode);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "Faltan datos"]),
            $response->body
        );
    }
    #[Test]
    public function it_should_return_error_if_credentials_are_incorrect()
    {
        $controller = new LoginController();

        // Simulamos una solicitud con credenciales incorrectas
        $input = json_encode(["username" => "admin", "password" => "wrongpass"]);
        file_put_contents("php://input", $input);

        $response = $controller();

        $this->assertEquals(400, $response->statusCode);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "Credenciales incorrectas"]),
            $response->body
        );
    }

    #[Test]
    public function it_should_return_token_if_credentials_are_correct()
    {
        $controller = new LoginController();


        $input = json_encode(["username" => "admin", "password" => "1234"]);
        file_put_contents("php://input", $input);


        $response = $controller();

        $this->assertEquals(400, $response->statusCode);
        $this->assertJson($response->body);

        $responseData = json_decode($response->body, true);
        $this->assertArrayHasKey('token', $responseData);


        $this->assertNotEmpty($responseData['token']);
    }

}