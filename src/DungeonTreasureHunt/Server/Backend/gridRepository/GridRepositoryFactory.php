<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;

interface GridRepositoryFactory
{
    public function createForUser(string $username): GridFileSystemRepository;
}