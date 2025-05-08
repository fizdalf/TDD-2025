<?php

namespace DungeonTreasureHunt\Backend\services;

final class Username
{
    private string $value;

    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 50;
    private const VALID_PATTERN = '/^[a-zA-Z0-9_]+$/';

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
        $length = strlen($this->value);
        return $length >= self::MIN_LENGTH &&
            $length <= self::MAX_LENGTH &&
            preg_match(self::VALID_PATTERN, $this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}