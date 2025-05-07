<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\models\UserGrids;

class GridFileSystemRepository implements GridRepository
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    private function getPath(string $username): string
    {
        return "{$this->basePath}/{$username}_gridSaved.txt";
    }

    public function loadGrids(string $username): array
    {
        $path = $this->getPath($username);
        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true);
    }

    private function saveGrids(string $username, array $grids): void
    {
        file_put_contents($this->getPath($username), json_encode($grids));
    }

    public function saveGrid(GridItem $gridItem): void
    {
        $storedGrids = $this->loadGrids($gridItem->username);
        $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;

        $storedGrids[$newId] = [
            "gridName" => $gridItem->name,
            "grid" => $gridItem->grid
        ];

        $this->saveGrids($gridItem->username, $storedGrids);
    }

    public function exists(string $username): bool
    {
        return file_exists($this->getPath($username));
    }

    public function delete(string $username): void
    {
        $path = $this->getPath($username);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function deleteGrid(GridItem $gridItem): void
    {
        $grids = $this->loadGrids($gridItem->username);

        if (!isset($gridItem->id)) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        if (!isset($grids[$gridItem->id])) {
            throw new GridNotFoundException("Grid no encontrado");
        }

        unset($grids[$gridItem->id]);
        $this->saveGrids($gridItem->username, $grids);
    }

    public function getGrid(string $username, int $id): ?GridItem
    {
        $grids = $this->loadGrids($username);

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
        $grids = $this->loadGrids($username);
        $gridItems = [];

        foreach ($grids as $id => $data) {
            $gridItems[] = new GridItem(
                $data['gridName'],
                $data['grid'],
                $username,
                $id
            );
        }

        return new UserGrids(...$gridItems);
    }
}