<?php

namespace DungeonTreasureHunt\Backend\services;

final class Password
{
    private string $value;
    private const MIN_LENGTH = 8;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isValid(): bool
    {
        return strlen($this->value) >= self::MIN_LENGTH;
    }

    public function hash(): HashedPassword
    {
        return new HashedPassword(password_hash($this->value, PASSWORD_DEFAULT));
    }
}