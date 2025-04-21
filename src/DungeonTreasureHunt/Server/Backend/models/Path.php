<?php

namespace DungeonTreasureHunt\Backend\models;

class Path
{

    private array $cameFrom = [];

    public function save(Position $position, PossibleMovement $data): void
    {
        $this->cameFrom[$this->getKey($position)] = $data;
    }

    public function get(Position $position): ?PossibleMovement
    {
        if (!isset($this->cameFrom[$this->getKey($position)])) {
            return null;
        }
        return $this->cameFrom[$this->getKey($position)];
    }

    private function getKey(Position $position): string
    {
        return "{$position->getX()},{$position->getY()}";
    }

}