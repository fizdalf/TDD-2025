<?php

namespace DungeonTreasureHunt\Backend\services;

class SimpleUserAuthenticator implements UserAuthenticator
{
    private array $users = [
        "admin" => "1234",
        "user" => "abcd"
    ];
    public function authenticate(string $username, string $password): bool
    {
        return isset($this->users[$username]) && $this->users[$username] === $password;
    }
}