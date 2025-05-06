<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\AuthenticatedUser;

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
    public function extractUser(Request $request): AuthenticatedUser
    {
        $authHeader = $request->getHeaders('Authorization') ?? null;

        if (!$authHeader || !str_starts_with($authHeader, "Bearer ")) {
            throw new InvalidTokenException('Invalid Token');
        }

        $token = substr($authHeader, 7);

        $user = $this->jwtUserExtractor->userFromToken($token);
        if (!isset($user)) {
            throw new InvalidTokenException('Invalid Token');
        }

        return $user;
    }
}