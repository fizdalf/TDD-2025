<?php

namespace DungeonTreasureHunt\Framework\services;

interface TokenHandlerInterface
{
    public function generateToken(mixed $user, int $expTime = 3600): string;

    public function verify(string $token): ?array;
}