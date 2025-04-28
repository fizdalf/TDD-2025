<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\models\GridItem;

interface GridRepository
{
    public function saveGrid(GridItem $gridItem);
}