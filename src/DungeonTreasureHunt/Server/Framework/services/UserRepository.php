<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Framework\services;

use DungeonTreasureHunt\Backend\services\Password;
use DungeonTreasureHunt\Backend\services\Username;

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

    public function saveUser(Username $username, Password $hashedPassword): void
    {
        $users = $this->getUsers();
        $users[$username->value()] = $hashedPassword->value();
        file_put_contents($this->path, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function userExists(Username $username): bool
    {
        $users = $this->getUsers();
        return isset($users[$username->value()]);
    }
}
