<?php

namespace DungeonTreasureHunt\Backend\models;

class UserGrids
{
    /**
     * @var GridItem[]
     */
    private array $grids;

    public function __construct(GridItem ...$grids)
    {
        $this->grids = $grids;
    }

    /** @return GridItem[] */
    public function all(): array
    {
        return $this->grids;
    }

    public function count(): int
    {
        return count($this->grids);
    }

    public function toArray(): array
    {
        return array_map(function (GridItem $gridItem) {
            return [
                'id' => $gridItem->id,
                'name' => $gridItem->name,
                'grid' => $gridItem->grid,
            ];
        }, $this->grids);
    }
}
