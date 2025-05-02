<?php

namespace DungeonTreasureHunt\Backend\models;

class GridItem
{
    public function __construct(
        public readonly string $name,
        public readonly array  $grid,
        public readonly string $username,
        public readonly ?int   $id = null
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'grid' => $this->grid,
            'username' => $this->username,
        ];
    }

}