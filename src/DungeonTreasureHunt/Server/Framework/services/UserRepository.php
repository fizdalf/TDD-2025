<?php

namespace DungeonTreasureHunt\Framework\services;

class UserRepository
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getUsers(): array
    {
        if (!file_exists($this->path)) {
            return [];
        }

        return json_decode(file_get_contents($this->path), true) ?? [];
    }

    public function saveUser(string $username, string $hashedPassword): void
    {
        $users = $this->getUsers();
        $users[$username] = $hashedPassword;
        file_put_contents($this->path, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function userExists(string $username): bool
    {
        $users = $this->getUsers();
        return isset($users[$username]);
    }
}
