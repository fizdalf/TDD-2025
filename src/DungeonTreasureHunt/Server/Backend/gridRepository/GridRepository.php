<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\models\GridItem;

interface GridRepository
{
    public function saveGrid(GridItem $gridItem);

    public function deleteGrid(GridItem $gridItem);

    public function getGrid(string $username, string $id): ?GridItem;
}