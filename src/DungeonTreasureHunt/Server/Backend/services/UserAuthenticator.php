<?php

namespace DungeonTreasureHunt\Backend\services;

interface UserAuthenticator
{
    public function authenticate(string $username, string $password): bool;
}