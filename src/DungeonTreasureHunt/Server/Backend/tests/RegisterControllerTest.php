<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\RegisterController;
use DungeonTreasureHunt\Backend\services\Username;
use DungeonTreasureHunt\Framework\http\APIResponse;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\services\UserRepository;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RegisterControllerTest extends TestCase
{
    private UserRepository $userRepository;
    private RegisterController $sut;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->sut = new RegisterController($this->userRepository);
    }

    #[Test]
    public function it_should_return_400_if_missing_credentials()
    {
        $request = new Request([], [], json_encode([
            'password' => 'securepass'
        ]));

        $response = $this->sut->__invoke($request);

        $expected = APIResponse::error('Faltan datos', 400);

        $this->assertEquals($expected, $response);
    }

    #[Test]
    public function it_should_return_409_if_user_already_exists()
    {
        $this->userRepository
            ->expects($this->once())
            ->method('userExists')
            ->with(new Username('admin'))
            ->willReturn(true);

        $request = new Request([], [], json_encode([
            'username' => 'admin',
            'password' => '1234'
        ]));

        $response = $this->sut->__invoke($request);

        $expected = APIResponse::error('El usuario ya existe', 409);

        $this->assertEquals($expected, $response);
    }

    #[Test]
    public function it_should_return_500_if_save_fails()
    {
        $this->userRepository->method('userExists')->with(new Username('newUser'))->willReturn(false);
        $this->userRepository->method('saveUser')->willThrowException(new Exception('Error writing file'));

        $request = new Request([], [], json_encode([
            'username' => 'newUser',
            'password' => 'contraseña'
        ]));

        $response = $this->sut->__invoke($request);

        $expected = ApiResponse::error('Error del servidor: Error writing file', 500);

        $this->assertEquals($expected->getStatus(), $response->getStatus());
        $this->assertEquals($expected->getBody(), $response->getBody());
    }

    #[Test]
    public function it_should_register_user_successfully()
    {
        $this->userRepository->method('userExists')->with(new Username('newuser'))->willReturn(false);

        $this->userRepository->expects($this->once())->method('saveUser')
            ->with(new Username('newuser'), $this->callback(function ($password) {
                return password_verify('securepass', $password->value());
            }));

        $request = new Request([], [], json_encode([
            'username' => 'newuser',
            'password' => 'securepass'
        ]));

        $response = $this->sut->__invoke($request);

        $expected = ApiResponse::success(['message' => 'Usuario registrado con éxito'], 201);
        $this->assertEquals($expected, $response);
    }
}