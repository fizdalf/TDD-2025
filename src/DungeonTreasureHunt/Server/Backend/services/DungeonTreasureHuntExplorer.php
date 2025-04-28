<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\models\Direction;
use DungeonTreasureHunt\Backend\models\Grid;
use DungeonTreasureHunt\Backend\models\Path;
use DungeonTreasureHunt\Backend\models\Position;
use DungeonTreasureHunt\Backend\models\PossibleMovement;
use DungeonTreasureHunt\Backend\models\Queue;
use DungeonTreasureHunt\Backend\models\Tile;
use DungeonTreasureHunt\Backend\models\VisitedTile;

class DungeonTreasureHuntExplorer
{
    private readonly MoveChecker $moveChecker;

    public function __construct()
    {
        $this->moveChecker = new MoveChecker();
    }

    /** @return PossibleMovement[] */
    public function findPathToTreasure(array $grid): array
    {
        $dungeon = new Grid($grid);
        $playerPosition = $dungeon->getPlayer();

        if ($playerPosition === null) {
            return [];
        }

        $queue = new Queue();
        $visitedTracker = new VisitedTile();
        $cameFrom = new Path();

        $queue->enqueue($playerPosition);

        while (!$queue->isEmpty()) {
            $currentPosition = $queue->dequeue();

            if (Tile::isTreasure($grid, $currentPosition)) {

                return $this->reconstructPath($cameFrom, $playerPosition, $currentPosition);
            }

            $possibleDirections = [
                Direction::Up,
                Direction::Right,
                Direction::Down,
                Direction::Left
            ];

            foreach ($possibleDirections as $direction) {
                $nextPosition = $currentPosition->move($direction);
                if ($this->moveChecker->isValidMove($nextPosition, $grid, $visitedTracker)) {

                    $cameFrom->save($nextPosition, new PossibleMovement($currentPosition, $direction));
                    $queue->enqueue($nextPosition);
                }
            }


            $visitedTracker->markAsVisited($currentPosition);
        }

        return [];
    }

    private function reconstructPath(Path $cameFrom, Position $startPosition, Position $endPosition): array
    {
        $path = [];
        $current = $endPosition;

        while ($current !== $startPosition) {
            $possibleMovement = $cameFrom->get($current);
            if ($possibleMovement === null) {
                break;
            }

            $path[] = $possibleMovement;
            $current = $possibleMovement->playerPosition;
        }
        return array_reverse($path);
    }

}