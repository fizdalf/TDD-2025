<?php
declare(strict_types=1);
require_once 'UserNotExistsException.php';

class UserLendingSystem
{
    private array $users = [];

    public function addUser(string $nombre, string $apellido, string $email): void
    {
        $this->users[$email] = $nombre .''. $apellido .''. $email;
    }

    /**
     * @throws UserNotExistsException
     */
    public function getUser(string $email): string
    {

        if (!isset($this->users[$email])) {
            throw new UserNotExistsException();
        }

        return $this->users[$email];
    }
}