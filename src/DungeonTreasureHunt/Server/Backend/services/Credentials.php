<?php

namespace DungeonTreasureHunt\Backend\services;

final class Credentials
{
    private Username $username;
    private Password $password;

    public function __construct(Username $username, Password $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['username'], $data['password'])) {
            throw new \InvalidArgumentException('Faltan datos de credenciales');
        }

        return new self(
            new Username($data['username']),
            new Password($data['password'])
        );
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function isComplete(): bool
    {
        return $this->username->isValid() && $this->password->isValid();
    }
}