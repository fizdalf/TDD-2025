<?php

namespace DungeonTreasureHunt\Backend\services;

class JWTUserExtractor
{
    private JwtHandler $jwtHandler;

    public function __construct(JwtHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    public function extractUsername(string $token): ?string
    {
        $payload = $this->jwtHandler->verifyToken($token);
        if (!$payload || !isset($payload['username'])) {
            return null;
        }
        return $payload['username'];
    }

    public function extractUserInfo(string $token): ?array
    {
        $payload = $this->jwtHandler->verifyToken($token);
        return $payload ?: null;
    }

}