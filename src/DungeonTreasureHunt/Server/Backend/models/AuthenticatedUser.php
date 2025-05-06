<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Backend\models;

class AuthenticatedUser
{

    public function __construct(public readonly string $name)
    {
    }

    public static function fromRaw(array $data): self
    {
        if (!isset($data['username'])) {
            throw new \RuntimeException('Invalid data, username is required');
        }
        return new self($data['username']);
    }

}