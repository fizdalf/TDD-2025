<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\models\GridItem;

interface GridRepository
{
    public function saveGrid(GridItem $gridItem);
}