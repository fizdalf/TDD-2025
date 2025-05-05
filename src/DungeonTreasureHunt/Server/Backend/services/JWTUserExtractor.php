<?php

namespace DungeonTreasureHunt\Backend\services;

class JWTUserExtractor
{
    private JwtVerifier $jwtVerifier;

    public function __construct(JwtVerifier $jwtVerifier)
    {
        $this->jwtVerifier = $jwtVerifier;
    }

    public function extractUsername(string $token): ?string
    {
        $payload = $this->jwtVerifier->verify($token);
        if (!$payload || !isset($payload['username'])) {
            return null;
        }
        return $payload['username'];
    }

    public function extractUserInfo(string $token): ?array
    {
        $payload = $this->jwtVerifier->verify($token);
        return $payload ?: null;
    }
}
