<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/JwtHandler.php';

class LoginControllerTest extends TestCase
{
    #[Test]
    public function it_should_login_successfully_with_valid_credentials()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'admin',
            'password' => '1234'
        ]);

        $controller = new LoginController();
        $response = $controller($mockRequest);

        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('token', $body);
        $this->assertNotEmpty($body['token']);

        $userData = JwtHandler::verifyToken($body['token']);
        $this->assertEquals('admin', $userData['username']);
    }

    #[Test]
    public function it_should_return_400_if_username_or_password_missing()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'admin'
        ]);

        $controller = new LoginController();
        $response = $controller($mockRequest);

        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_user_does_not_exist()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'not_a_user',
            'password' => 'whatever'
        ]);

        $controller = new LoginController();
        $response = $controller($mockRequest);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_password_is_wrong()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'admin',
            'password' => 'wrong_password'
        ]);

        $controller = new LoginController();
        $response = $controller($mockRequest);

        $this->assertEquals(401, $response->getStatus());
    }
}