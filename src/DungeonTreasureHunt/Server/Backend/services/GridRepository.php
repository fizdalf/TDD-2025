<?php

namespace DungeonTreasureHunt\Backend\services;

class GridRepository
{
    public function __construct(private string $username){}

    private function getPath(): string
    {
        return __DIR__ . "/../data/{$this->username}_gridSaved.txt";
    }

    public function loadGrids(): array
    {
        $path = $this->getPath();
        if (!file_exists($path)){
            return [];
        }

        return json_decode(file_get_contents($path), true);
    }

    public function saveGrids(array $grids): void
    {
        file_put_contents($this->getPath(), json_encode($grids));
    }

    public function exists(): bool
    {
        return file_exists($this->getPath());
    }
}