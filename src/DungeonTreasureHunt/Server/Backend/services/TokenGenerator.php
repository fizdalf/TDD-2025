<?php

namespace DungeonTreasureHunt\Backend\services;

interface TokenGenerator
{
    public function generateToken(array $payload): string;
}