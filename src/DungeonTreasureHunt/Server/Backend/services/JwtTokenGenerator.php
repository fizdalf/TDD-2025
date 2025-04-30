<?php

namespace DungeonTreasureHunt\Backend\services;

class JwtTokenGenerator implements TokenGenerator
{
    private JwtHandler $jwtHandler;

    public function __construct(JwtHandler $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    public function generateToken(array $payload): string
    {
        return $this->jwtHandler->generateToken($payload);
    }
}