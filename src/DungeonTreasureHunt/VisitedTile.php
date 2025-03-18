<?php

namespace DungeonTreasureHunt;

class VisitedTile
{
    private array $visitedTiles = [];

    public function hasVisited(Position $tile): bool
    {
        return isset($this->visitedTiles[$tile->x]);
    }

    public function  markAsVisited(Position $tile): void
    {
        $this->visitedTiles[$tile->x] = true;
    }
}