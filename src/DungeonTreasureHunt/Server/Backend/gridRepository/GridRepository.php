<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\models\UserGrids;

interface GridRepository
{
    public function saveGrid(GridItem $gridItem);

    public function deleteGrid(GridItem $gridItem);

    public function getGrid(string $username,int $id): ?GridItem;

    public function getAllGrids(string $username): UserGrids;
}