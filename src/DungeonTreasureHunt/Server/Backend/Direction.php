<?php

namespace DungeonTreasureHunt\Backend;

enum Direction implements \JsonSerializable
{
    case Left;
    case Right;
    case Up;
    case Down;

    public function jsonSerialize(): mixed
    {
        return $this->name;
    }
}