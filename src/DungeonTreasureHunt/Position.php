<?php
declare(strict_types=1);

namespace DungeonTreasureHunt;

class Position
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    )
    {
    }

    public function move(Direction $direction): Position
    {

        $movement = match ($direction) {
            Direction::Left => [-1, 0],
            Direction::Right => [1, 0],
            Direction::Up => [0, -1],
            Direction::Down => [0, 1],
        };

        [$dx, $dy] = $movement;

        return new self(
            $this->x + $dx, $this->y + $dy
        );
    }
}