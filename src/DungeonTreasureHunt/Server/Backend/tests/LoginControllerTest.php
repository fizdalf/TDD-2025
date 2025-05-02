<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;
use DungeonTreasureHunt\Backend\services\TokenGenerator;
use DungeonTreasureHunt\Backend\services\UserAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoginControllerTest extends TestCase
{
    private ResponseBuilder $responseBuilder;
    private TokenGenerator $tokenGenerator;
    private UserAuthenticator $userAuthenticator;

    protected function setUp(): void
    {
        $this->responseBuilder = $this->createMock(ResponseBuilder::class);

        $this->responseBuilder->method('success')->willReturnCallback(
            function($data = []) {
                return (new Response(200))->withJson(['status' => 'success', ...$data]);
            }
        );
        $this->responseBuilder->method('error')->willReturnCallback(
            function($message, $statusCode) {
                return (new Response($statusCode))->withJson(['status' => 'error', 'error' => $message]);
            }
        );

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
            $this->responseBuilder,
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
            $this->responseBuilder,
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($mockRequest);

        $this->assertEquals(400, $response->getStatus());
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
            $this->responseBuilder,
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
            $this->responseBuilder,
            $this->tokenGenerator,
            $this->userAuthenticator
        );

        $response = $controller($mockRequest);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
    }
}