<?php

namespace DungeonTreasureHunt\Framework\services;

use DungeonTreasureHunt\Framework\models\AuthenticatedUser;

class JWTUserExtractor
{
    private JwtVerifier $jwtVerifier;

    public function __construct(JwtVerifier $jwtVerifier)
    {
        $this->jwtVerifier = $jwtVerifier;
    }
    public function userFromToken(string $token): ?AuthenticatedUser
    {
        $payload = $this->jwtVerifier->verify($token);
        if (!$payload) {
            return null;
        }
        try {
            return AuthenticatedUser::fromRaw($payload);
        } catch (\Throwable) {
            return null;
        }
    }
}
