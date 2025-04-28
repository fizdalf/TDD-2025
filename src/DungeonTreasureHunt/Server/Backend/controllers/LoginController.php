<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;

class LoginController
{
    private array $users = [
        "admin" => "1234",
        "user" => "abcd"
    ];

    public function __invoke(Request $request): Response
    {
        $credentials = $this->extractCredentials($request);

        if (!$this->areCredentialsComplete($credentials)) {
            return $this->handleIncompleteCredentials();
        }

        if (!$this->areCredentialsValid($credentials['username'], $credentials['password'])) {
            return $this->handleInvalidCredentials();
        }

        return $this->generateSuccessResponse($credentials['username']);
    }

    private function extractCredentials(Request $request): array
    {
        return $request->parseBodyAsJson();
    }

    private function areCredentialsComplete(array $credentials): bool
    {
        return isset($credentials['username'], $credentials['password']);
    }

    private function areCredentialsValid(string $username, string $password): bool
    {
        return isset($this->users[$username]) && $this->users[$username] === $password;
    }

    private function handleIncompleteCredentials(): Response
    {
        return JsonResponseBuilder::error("Faltan datos", 400);
    }

    private function handleInvalidCredentials(): Response
    {
        return JsonResponseBuilder::error("Credenciales incorrectas", 401);
    }

    private function generateSuccessResponse(string $username): Response
    {
        $token = $this->generateToken($username);
        return JsonResponseBuilder::success(["token" => $token]);
    }

    private function generateToken(string $username): string
    {
        return JwtHandler::generateToken(["username" => $username]);
    }
}