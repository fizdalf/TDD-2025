<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\models\Position;
use DungeonTreasureHunt\Backend\models\Tile;
use DungeonTreasureHunt\Backend\models\VisitedTile;

class MoveChecker
{
    public function isValidMove(Position $playerNextPosition, array $grid, VisitedTile $visitedTile): bool
    {
        $x = $playerNextPosition->x;
        $y = $playerNextPosition->y;


        if ($y < 0 || $y >= count($grid)) {
            return false;
        }

        if ($x < 0 || $x >= count($grid[$y])) {
            return false;
        }

        $nextTile = $grid[$y][$x];

        if ($nextTile === Tile::Wall) {
            return false;
        }

        if ($visitedTile->hasVisited($playerNextPosition)) {
            return false;
        }

        return true;
    }
}