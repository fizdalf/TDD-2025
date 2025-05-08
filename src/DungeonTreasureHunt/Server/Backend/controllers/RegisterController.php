<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\Password;
use DungeonTreasureHunt\Backend\services\Username;
use DungeonTreasureHunt\Framework\http\ApiResponse;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\http\Response;
use DungeonTreasureHunt\Framework\services\UserRepository;

class RegisterController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): Response
    {
        $credentials = $this->extractCredentials($request);

        if (!$this->areCredentialsComplete($credentials)) {
            return ApiResponse::error("Faltan datos", 400);
        }

        $usernameValue = $credentials['username'];
        $passwordValue = $credentials['password'];

        $username = new Username($usernameValue);

        if ($this->userRepository->userExists($username)) {
            return ApiResponse::error("El usuario ya existe", 409);
        }

        try {
            $hashedPassword = password_hash($passwordValue, PASSWORD_DEFAULT);
            $password = new Password($hashedPassword);
            $this->userRepository->saveUser($username, $password);
        } catch (\Exception $e) {
            return ApiResponse::error("Error del servidor: " . $e->getMessage(), 500);
        }

        return ApiResponse::success(["message" => "Usuario registrado con éxito"], 201);
    }

    private function extractCredentials(Request $request): array
    {
        return $request->parseBodyAsJson();
    }

    private function areCredentialsComplete(array $credentials): bool
    {
        return isset($credentials['username'], $credentials['password']);
    }
}
