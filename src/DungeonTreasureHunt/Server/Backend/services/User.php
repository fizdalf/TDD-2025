<?php

namespace DungeonTreasureHunt\Backend\services;

final class User
{
    private Username $username;
    private HashedPassword $password;

    public function __construct(Username $username, HashedPassword $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public static function register(Username $username, Password $password): self
    {
        return new self($username, $password->hash());
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }
}