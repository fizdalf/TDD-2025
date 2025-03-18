<?php
declare(strict_types=1);

namespace DungeonTreasureHunt;

class Position
{
    public function __construct(public readonly int $x)
    {
    }

    public function move(Direction $direction): Position
    {
        return new self(
            $this->x + $direction->value
        );
    }
}