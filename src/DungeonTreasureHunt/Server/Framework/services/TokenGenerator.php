<?php

namespace DungeonTreasureHunt\Framework\services;

interface TokenGenerator
{
    public function generateToken(array $payload): string;
}