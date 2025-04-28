<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;

class DungeonTreasureHunt
{
    private DungeonTreasureHuntExplorer $dungeonTreasureHuntExplorer;

    public function __construct()
    {
        $this->dungeonTreasureHuntExplorer = new DungeonTreasureHuntExplorer();
    }

    public function canReachTreasure(array $grid): bool
    {
        return count($this->dungeonTreasureHuntExplorer->findPathToTreasure($grid)) > 0;
    }
}