<?php

namespace DungeonTreasureHunt\Backend;

class VisitedTile
{
    private array $visitedTiles = [];

    public function hasVisited(Position $tile): bool
    {
        $section = $this->getPositionSection($tile);
        return isset($this->visitedTiles[$section]);
    }

    public function markAsVisited(Position $tile): void
    {
        $section = $this->getPositionSection($tile);
        $this->visitedTiles[$section] = true;
    }

    public function getPositionSection(Position $tile): string{
        return $tile->x . ',' . $tile->y;
    }
}