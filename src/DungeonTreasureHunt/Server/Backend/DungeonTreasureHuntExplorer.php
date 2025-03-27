<?php

namespace DungeonTreasureHunt\Backend;

require_once 'Direction.php';
require_once 'Tile.php';
require_once 'PossibleMovement.php';
require_once 'VisitedTile.php';
require_once 'Queue.php';
require_once 'Position.php';
require_once 'MoveChecker.php';
require_once 'Grid.php';
require_once 'Path.php';

class DungeonTreasureHuntExplorer
{
    private readonly MoveChecker $moveChecker;

    public function __construct()
    {
        $this->moveChecker = new MoveChecker();
    }

    public function findPathToTreasure(array $grid): array
    {
        error_log("Grid recibido: " . print_r($grid, true));

        $dungeon = new Grid($grid);
        $playerPosition = $dungeon->getPlayer();

        if ($playerPosition === null) {
            error_log("No se encontró al jugador");
            return [];
        }

        $queue = new Queue();
        $visitedTracker = new VisitedTile();
        $cameFrom = new Path();

        $queue->enqueue($playerPosition);

        while (!$queue->isEmpty()) {
            $currentPosition = $queue->dequeue();

            // Log de la posición actual
            error_log("Visitando: (" . $currentPosition->getX() . ", " . $currentPosition->getY() . ")");

            if (Tile::isTreasure($grid, $currentPosition)) {
                return $this->reconstructPath($cameFrom, $currentPosition);
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

        return [];  // Si no se encuentra un camino
    }


    private function reconstructPath(Path $cameFrom, Position $endPosition): array
    {
        $path = [];
        $current = $endPosition;

        while (($possibleMovement = $cameFrom->get($current)) !== null) {
            $path[] = new PossibleMovement($current, $possibleMovement->direction);
            $current = $possibleMovement->playerPosition;
        }

        return array_reverse($path);
    }


}