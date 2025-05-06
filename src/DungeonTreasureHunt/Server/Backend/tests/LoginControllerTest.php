<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Framework\http\APIResponse;
use DungeonTreasureHunt\Backend\controllers\LoginController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\services\TokenGenerator;
use DungeonTreasureHunt\Framework\services\UserAuthenticator;

class LoginControllerTest extends TestCase
{
    private TokenGenerator $tokenGenerator;
    private UserAuthenticator $userAuthenticator;

    protected function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(TokenGenerator::class);
        $this->userAuthenticator = $this->createMock(UserAuthenticator::class);
    }

    #[Test]
    public function test_it_should_login_successfully_with_valid_credentials(): void
    {
        $tokenGeneratorMock = $this->createMock(TokenGenerator::class);
        $userAuthenticatorMock = $this->createMock(UserAuthenticator::class);
        $requestMock = $this->createMock(Request::class);

        $requestMock->method('parseBodyAsJson')->willReturn([
            'username' => 'admin',
            'password' => 'secret',
        ]);

        $userAuthenticatorMock
            ->method('authenticate')
            ->with('admin', 'secret')
            ->willReturn(true);

        $tokenGeneratorMock
            ->method('generateToken')
            ->with(['username' => 'admin'])
            ->willReturn('token---');

        $controller = new LoginController($tokenGeneratorMock, $userAuthenticatorMock);

        $response = $controller($requestMock);

        $expected = ApiResponse::success(['token' => 'token---']);
        $this->assertEquals($expected, $response);
    }
    #[Test]
    public function it_should_return_400_if_password_missing()
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
    public function it_should_return_400_if_username_missing()
    {
        $request = new Request([], [], json_encode([
            'password' => 'secret123'
            // username intentionally missing
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
    public function it_should_return_401_if_cannot_authenticate_user()
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
