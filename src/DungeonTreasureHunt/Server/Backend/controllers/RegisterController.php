<?php

namespace DungeonTreasureHunt\Backend\controllers;

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

        $username = $credentials['username'];
        $password = $credentials['password'];

        if ($this->userRepository->userExists($username)) {
            return ApiResponse::error("El usuario ya existe", 409);
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            //TODO: Investigate about "primitive obsession", and how to improve this bit of code
            $this->userRepository->saveUser($hashedPassword, $username);
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
