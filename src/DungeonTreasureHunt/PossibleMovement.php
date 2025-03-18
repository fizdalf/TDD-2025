<?php

namespace DungeonTreasureHunt;


class PossibleMovement
{
    public Position $playerPosition;
    public Direction $direction;

    public function __construct(Position $playerPosition, Direction $direction)
    {
        $this->playerPosition = $playerPosition;
        $this->direction = $direction;
    }
}