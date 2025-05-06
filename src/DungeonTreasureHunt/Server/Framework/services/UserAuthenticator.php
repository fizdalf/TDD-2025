<?php

namespace DungeonTreasureHunt\Framework\services;

interface UserAuthenticator
{
    public function authenticate(string $username, string $password): bool;
}