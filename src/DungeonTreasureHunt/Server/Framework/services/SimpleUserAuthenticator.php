<?php

namespace DungeonTreasureHunt\Framework\services;

class SimpleUserAuthenticator implements UserAuthenticator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function authenticate(string $username, string $password): bool
    {
        $users = $this->userRepository->getUsers();

        if (!isset($users[$username])) {
            return false;
        }

        return password_verify($password, $users[$username]);
    }
}