<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\models\GridItem;

class GridFileSystemRepository implements GridRepository
{
    public function __construct(private string $username)
    {
    }


    private function getPath(): string
    {
        return __DIR__ . "/../data/{$this->username}_gridSaved.txt";
    }

    public function loadGrids(): array
    {
        $path = $this->getPath();
        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true);
    }

    public function saveGrid(GridItem $gridItem): void
    {

        $this->username = $gridItem->username;
        $storedGrids = $this->loadGrids();
        $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;

        $storedGrids[$newId] = [
            "gridName" => $gridItem->name,
            "grid" => $gridItem->grid
        ];

        $this->saveGrids($storedGrids);
    }

    public function saveGrids(array $grids): void
    {
        file_put_contents($this->getPath(), json_encode($grids));
    }

    public function exists(): bool
    {
        return file_exists($this->getPath());
    }

    public function delete(): void
    {
        $path = $this->getPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }
}