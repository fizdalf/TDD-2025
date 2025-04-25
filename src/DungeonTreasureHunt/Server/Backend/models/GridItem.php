<?php

namespace DungeonTreasureHunt\Backend\models;

class GridItem
{

    public function __construct(
        public readonly string $name,
        public readonly array  $grid,
        public readonly string $username
    )
    {
    }
}