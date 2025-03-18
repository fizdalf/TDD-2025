<?php
declare(strict_types=1);

namespace DungeonTreasureHunt;

require_once 'Direction.php';
require_once 'Tile.php';
require_once 'PossibleMovement.php';
require_once 'VisitedTile.php';
require_once 'Queue.php';
require_once 'Position.php';

class DungeonTreasureHunt
{

    public function __construct()
    {
    }

    public function canReachTreasure(array $grid): bool
    {
        if (empty($grid)) {
            return false;
        }

        $row = $grid[0];
        if (empty($row)) {
            return false;
        }


        $playerPosition = array_search(Tile::Player, $row);

        if ($playerPosition === false) {
            return false;
        }

        $visitedTracker = new VisitedTile();
        $canReachTreasure = $this->canReachTreasureDirectionBetter(new Position($playerPosition), $row, $visitedTracker);
        return $canReachTreasure;
    }

    private function canMakeMove(Position $playerPosition, Direction $direction, array $row, VisitedTile $visitedTile): bool
    {
        $playerNextPosition = $playerPosition->move($direction);
        if ($playerNextPosition->x >= count($row) || $playerNextPosition->x < 0) {
            return false;
        }

        $nextTile = $row[$playerNextPosition->x];

        if ($nextTile === Tile::Wall) {
            return false;
        }

        if ($visitedTile->hasVisited($playerNextPosition)) {
            return false;
        }


        return true;
    }

    private function possibleMovements(Position $playerPosition, array $row, VisitedTile $visitedTile): array
    {
        $possibleMovements = [];

        $possibleDirections = [
            Direction::Left,
            Direction::Right,
        ];

        foreach ($possibleDirections as $direction) {
            $canMakeMove = $this->canMakeMove($playerPosition, $direction, $row, $visitedTile);
            if (!$canMakeMove) {
                continue;
            }

            $possibleMovements[] = new PossibleMovement($playerPosition, $direction);
        }
        return $possibleMovements;
    }

    private function isTreasure(Position $playerPosition, array $row): bool
    {
        return $row[$playerPosition->x] === Tile::Treasure;
    }


    private function canReachTreasureDirectionBetter(Position $playerPosition, array $row, VisitedTile $visitedTracker): bool
    {
        $queue = new Queue();

        $currentTile = $playerPosition;

        do {
            $isCurrentTileATreasure = $this->isTreasure($currentTile, $row);

            $possibleMovements = $this->possibleMovements($currentTile, $row, $visitedTracker);

            $visitedTracker->markAsVisited($currentTile);

            foreach ($possibleMovements as $possibleMovement) {
                $queue->enqueue($possibleMovement);
            }
            /** @var PossibleMovement|false $movement */
            $movement = $queue->dequeue();

            if ($movement) {
                $currentTile = $movement->playerPosition->move($movement->direction);
            }

        } while (!$isCurrentTileATreasure && $movement);

        return $isCurrentTileATreasure;
    }

}