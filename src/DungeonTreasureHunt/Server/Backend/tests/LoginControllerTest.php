<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilderAdapter;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
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
            function($payload) {
                return JwtHandler::generateToken($payload);
            }
        );

        $this->userAuthenticator = $this->createMock(UserAuthenticator::class);
    }

    #[Test]
    public function it_should_login_successfully_with_valid_credentials()
    {
        $this->userAuthenticator->method('authenticate')->willReturnCallback(
            function($username, $password) {
                return $username === 'admin' && $password === '1234';
            }
        );

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'admin',
            'password' => '1234'
        ]);

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

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

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($mockRequest);

        $this->assertEquals(400, $response->getStatus());
        // body?
    }

    #[Test]
    public function it_should_return_401_if_user_does_not_exist()
    {
        $this->userAuthenticator->method('authenticate')->willReturn(false);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'not_a_user',
            'password' => 'whatever'
        ]);

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($mockRequest);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
    }

    #[Test]
    public function it_should_return_401_if_password_is_wrong()
    {
        $this->userAuthenticator->method('authenticate')->willReturn(false);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('parseBodyAsJson')->willReturn([
            'username' => 'admin',
            'password' => 'wrong_password'
        ]);

        $controller = new LoginController(
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($mockRequest);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
    }
}