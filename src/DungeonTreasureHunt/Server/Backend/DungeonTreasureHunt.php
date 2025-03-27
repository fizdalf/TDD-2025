<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Backend;

require_once 'Direction.php';
require_once 'Tile.php';
require_once 'PossibleMovement.php';
require_once 'VisitedTile.php';
require_once 'Queue.php';
require_once 'Position.php';
require_once 'DungeonTreasureHuntExplorer.php';
require_once 'MoveChecker.php';

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