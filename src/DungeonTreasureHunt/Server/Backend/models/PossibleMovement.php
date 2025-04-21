<?php

namespace DungeonTreasureHunt\Backend\models;


class PossibleMovement implements \JsonSerializable
{
    public Position $playerPosition;
    public ?Direction $direction;

    public function __construct(Position $playerPosition, ?Direction $direction = null)
    {
        $this->playerPosition = $playerPosition;
        $this->direction = $direction;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "playerPosition" => $this->playerPosition,
            "direction" => $this->direction
        ];
    }
}