<?php

namespace DungeonTreasureHunt\Framework\services;

interface JwtVerifier
{
    public function verify(string $token): array | bool |null;
}