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

        $playerPosition = null;

        foreach ($grid as $y => $row){
            $x = array_search(Tile::Player, $row);
            if($x !== false){
                $playerPosition = new Position((int)$x, $y);
                break;
            }
        }

        if ($playerPosition === null) {
            return false;
        }

        $visitedTracker = new VisitedTile();
        $canReachTreasure = $this->canReachTreasureDirectionBetter($playerPosition, $grid, $visitedTracker);
        return $canReachTreasure;
    }

    private function canMakeMove(Position $playerPosition, Direction $direction, array $grid, VisitedTile $visitedTile): bool
    {
        $playerNextPosition = $playerPosition->move($direction);
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

    private function possibleMovements(Position $playerPosition, array $grid, VisitedTile $visitedTile): array
    {
        $possibleMovements = [];

        $possibleDirections = [
            Direction::Left,
            Direction::Right,
            Direction::Up,
            Direction::Down
        ];

        foreach ($possibleDirections as $direction) {
            $canMakeMove = $this->canMakeMove($playerPosition, $direction, $grid, $visitedTile);
            if (!$canMakeMove) {
                continue;
            }

            $possibleMovements[] = new PossibleMovement($playerPosition, $direction);
        }
        return $possibleMovements;
    }


    private function isTreasure(Position $playerPosition, array $grid): bool
    {
        return $grid[$playerPosition->y][$playerPosition->x] === Tile::Treasure;
    }


    private function canReachTreasureDirectionBetter(Position $playerPosition, array $grid, VisitedTile $visitedTracker): bool
    {
        $queue = new Queue();

        $currentTile = $playerPosition;

        do {
            $isCurrentTileATreasure = $this->isTreasure($currentTile, $grid);

            $possibleMovements = $this->possibleMovements($currentTile, $grid, $visitedTracker);

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