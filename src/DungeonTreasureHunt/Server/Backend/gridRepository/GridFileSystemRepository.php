<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\models\UserGrids;

class GridFileSystemRepository implements GridRepository
{
    public function __construct(private string $username)
    {
    }


    private function getPath(): string
    {
        return __DIR__ . "/../data/{$this->username}_gridSaved.txt";
    }

    private function loadGrids(): array
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

    private function saveGrids(array $grids): void
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

    public function deleteGrid(GridItem $gridItem): void
    {
        $this->username = $gridItem->username;
        $grids = $this->loadGrids();

        if (!isset($gridItem->id)) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        if (!isset($grids[$gridItem->id])) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        unset($grids[$gridItem->id]);
        $this->saveGrids($grids);
    }

    public function getGrid(string $username, int $id): ?GridItem
    {
        $this->username = $username;
        $grids = $this->loadGrids();

        if (count($grids) === 0) {
            return null;
        }
        if (!isset($grids[$id])) {
            return null;
        }
        $gridData = $grids[$id];

        return new GridItem(
            $gridData['gridName'],
            $gridData['grid'],
            $username,
            $id
        );
    }

    public function getAllGrids(string $username): UserGrids
    {
        $this->username = $username;
        $grids = $this->loadGrids();
        $gridItems = [];

        foreach ($grids as $id => $data) {
            $gridItems[] = new GridItem(
                $data['gridName'],
                $data['grid'],
                $username,
                $id
            );
        }

        return new UserGrids($gridItems);
    }
}