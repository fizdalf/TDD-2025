<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\TokenGenerator;
use DungeonTreasureHunt\Backend\services\UserAuthenticator;
use DungeonTreasureHunt\Backend\http\Request;

class LoginController
{

    private TokenGenerator $tokenGenerator;
    private UserAuthenticator $userAuthenticator;

    public function __construct(

        TokenGenerator $tokenGenerator,
        UserAuthenticator $userAuthenticator
    ) {

        $this->tokenGenerator = $tokenGenerator;
        $this->userAuthenticator = $userAuthenticator;
    }

    public function __invoke(Request $request): Response
    {
        $credentials = $this->extractCredentials($request);

        if (!$this->areCredentialsComplete($credentials)) {
            return $this->handleIncompleteCredentials();
        }

        if (!$this->userAuthenticator->authenticate($credentials['username'], $credentials['password'])) {
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
        $token = $this->tokenGenerator->generateToken(["username" => $username]);
        return JsonResponseBuilder::success(["token" => $token]);
    }
}