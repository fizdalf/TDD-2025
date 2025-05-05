<?php

namespace DungeonTreasureHunt\Backend\controllers;

use DungeonTreasureHunt\Backend\http\ApiResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\http\Response;
use DungeonTreasureHunt\Backend\services\TokenGenerator;
use DungeonTreasureHunt\Backend\services\UserAuthenticator;

class LoginController
{

    private TokenGenerator $tokenGenerator;
    private UserAuthenticator $userAuthenticator;

    public function __construct(

        TokenGenerator    $tokenGenerator,
        UserAuthenticator $userAuthenticator
    )
    {

        $this->tokenGenerator = $tokenGenerator;
        $this->userAuthenticator = $userAuthenticator;
    }

    public function __invoke(Request $request): Response
    {
        $credentials = $this->extractCredentials($request);

        if (!$this->areCredentialsComplete($credentials)) {
            return APIResponse::error("Faltan datos", 400);
        }

        if (!$this->userAuthenticator->authenticate($credentials['username'], $credentials['password'])) {
            return APIResponse::error("Credenciales incorrectas", 401);
        }

        $token = $this->tokenGenerator->generateToken(["username" => $credentials['username']]);
        return APIResponse::success(["token" => $token]);
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