<?php

namespace DungeonTreasureHunt\Backend\models;

enum Tile : string
{
    public const Treasure = 'T';
    public const Wall = '#';
    public const Path = '.';
    public const Player = 'P';

    public static function isTreasure(array $grid, Position $position): bool
    {
        return isset($grid[$position->y][$position->x]) && $grid[$position->y][$position->x] === self::Treasure;
    }
}
