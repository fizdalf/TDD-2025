<?php

namespace DungeonTreasureHunt\Backend;

class MoveChecker
{
    public function isValidMove(Position $playerNextPosition, array $grid, VisitedTile $visitedTile): bool
    {
        $x = $playerNextPosition->x;
        $y = $playerNextPosition->y;


        if ($y >= count($grid) || $y < 0 || $x >= count($grid[$y]) || $x < 0) {
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