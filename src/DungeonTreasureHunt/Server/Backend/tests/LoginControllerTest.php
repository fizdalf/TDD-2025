<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\APIResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JsonResponse;
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
        $this->userAuthenticator
            ->expects($this->once())
            ->method('authenticate')
            ->with('admin', '1234')
            ->willReturn(true);

        $this->tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->with(["username" => 'admin'])
            ->willReturn('token---');


        $request = new Request([], [], json_encode([
            'username' => 'admin',
            'password' => '1234'
        ]));

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($request);

        $expectedResponse = APIResponse::success(['token' => 'token---']);

        $this->assertEquals($expectedResponse, $response);
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

        $expectedResponse = APIResponse::error('Faltan datos');

        $this->assertEquals($expectedResponse, $response);
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

        $expectedResponse = APIResponse::error('Credenciales incorrectas', 401);

        $this->assertEquals($expectedResponse, $response);
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

        $expectedResponse = APIResponse::error('Credenciales incorrectas', 401);

        $this->assertEquals($expectedResponse, $response);
    }
}
