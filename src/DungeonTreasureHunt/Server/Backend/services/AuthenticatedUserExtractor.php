<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\http\Request;

class AuthenticatedUserExtractor
{
    private JWTUserExtractor $jwtUserExtractor;

    public function __construct(JWTUserExtractor $jwtUserExtractor)
    {
        $this->jwtUserExtractor = $jwtUserExtractor;
    }

    /**
     * @throws InvalidTokenException
     */
    public function extractUser(Request $request): array
    {
        $authHeader = $request->getHeaders('Authorization') ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            throw new InvalidTokenException('Invalid Token');
        }

        $token = substr($authHeader, 7);
        $user = $this->jwtUserExtractor->extractUserInfo($token);

        if (!isset($user) || !isset($user['username'])) {
            throw new InvalidTokenException('Invalid Token');
        }

        return $user;
    }
}