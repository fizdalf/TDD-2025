<?php

namespace DungeonTreasureHunt\Backend\services;

interface JwtVerifier
{
    public function verify(string $token): array | bool |null;
}