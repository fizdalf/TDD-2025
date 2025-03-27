<?php


namespace DungeonTreasureHunt\Backend;

use DungeonTreasureHunt\Backend;

class Grid
{

    public function __construct(private readonly array $dungeon)
    {
    }


    public static function fromArray(array $textGrid)
    {
        return new self($textGrid);

    }

    public function getPlayer(): ?Backend\Position
    {
        foreach ($this->dungeon as $y => $row) {
            $x = array_search(Tile::Player, $row, true);
            if ($x !== false) {
                return new Position((int)$x, $y);
            }
        }
        return null;
    }
}