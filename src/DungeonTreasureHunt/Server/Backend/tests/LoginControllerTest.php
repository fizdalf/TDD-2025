<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\TokenGenerator;
use DungeonTreasureHunt\Backend\services\UserAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoginControllerTest extends TestCase
{
    private TokenGenerator $tokenGenerator;
    private UserAuthenticator $userAuthenticator;

    protected function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(TokenGenerator::class);
        $this->tokenGenerator->method('generateToken')->willReturnCallback(
            fn($payload) => JwtHandler::generateToken($payload)
        );

        $this->userAuthenticator = $this->createMock(UserAuthenticator::class);
    }

    #[Test]
    public function it_should_login_successfully_with_valid_credentials()
    {
        $this->userAuthenticator->method('authenticate')->willReturnCallback(
            fn($username, $password) => $username === 'admin' && $password === '1234'
        );

        $request = new Request([], [], json_encode([
            'username' => 'admin',
            'password' => '1234'
        ]));

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($request);

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
        $request = new Request([], [], json_encode([
            'username' => 'admin'
        ]));

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());

        $expectedBody = [
            'status' => 'error',
            'error' => 'Faltan datos'
        ];

        $actualBody = json_decode($response->getBody(), true);
        $this->assertEquals($expectedBody, $actualBody);
    }

    #[Test]
    public function it_should_return_401_if_user_does_not_exist()
    {
        $this->userAuthenticator->method('authenticate')->willReturn(false);

        $request = new Request([], [], json_encode([
            'username' => 'not_a_user',
            'password' => 'whatever'
        ]));

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());

        $expectedBody = [
            'status' => 'error',
            'error' => 'Credenciales incorrectas'
        ];

        $actualBody = json_decode($response->getBody(), true);
        $this->assertEquals($expectedBody, $actualBody);
    }

    #[Test]
    public function it_should_return_401_if_password_is_wrong()
    {
        $this->userAuthenticator->method('authenticate')->willReturn(false);

        $request = new Request([], [], json_encode([
            'username' => 'admin',
            'password' => 'wrong_password'
        ]));

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());

        $expectedBody = [
            'status' => 'error',
            'error' => 'Credenciales incorrectas'
        ];

        $actualBody = json_decode($response->getBody(), true);
        $this->assertEquals($expectedBody, $actualBody);
    }
}
